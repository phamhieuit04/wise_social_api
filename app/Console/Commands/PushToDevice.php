<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Services\FirebaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PushToDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PushToDevice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //list all notification
        $notifications = Notification::select(
            'notifications.id', 'notifications.user_id', 'notifications.action_id', 'notifications.content', 'device_tokens.token'
        )->leftJoin(
            'device_tokens',
            'notifications.user_id',
            'device_tokens.user_id'
        )
        ->where('status', Notification::STATUS_WAIT)
        ->orderBy('id', 'ASC')
        ->take(100)->get();
        if(count($notifications) <= 0){
            return null;
        }
        $firebaseService = new FirebaseService();
        foreach ($notifications as $notification) {
            // Handle missing device token
            if (is_null($notification->token)) {
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update([
                        'status' => Notification::STATUS_FAIL
                    ]);
                $this->error("Notification {$notification->id} failed: Device token is missing.");
            } else {
                // Send notification to user device via FCM
                $sendFCM = $firebaseService->sendFCM($notification->content, $notification->token);
                if ($sendFCM) {
                    // Update notification status to done if sent successfully
                    DB::table('notifications')
                        ->where('id', $notification->id)
                        ->update([
                            'status' => Notification::STATUS_DONE
                        ]);
                    $this->info("Notification {$notification->id} sent successfully to device.");
                } else {
                    // Update notification status to fail if sending failed
                    DB::table('notifications')
                        ->where('id', $notification->id)
                        ->update([
                            'status' => Notification::STATUS_FAIL
                        ]);
                    $this->error("Notification {$notification->id} failed: Failed to send FCM notification.");
                }
            }
        }
    }
}
