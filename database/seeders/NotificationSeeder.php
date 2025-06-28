<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 10; $i++) {
                $userId = fake()->numberBetween(1, 10);
                $actionId = fake()->numberBetween(1, 10);
                while ($actionId == $userId) {
                    $actionId = fake()->numberBetween(1, 10);
                }
                DB::table('notifications')->insert([
                    'user_id' => $userId,
                    'action_id' => $actionId,
                    'content' => fake()->sentence(),
                    'is_view' => fake()->numberBetween(1, 2),
                    'status' => fake()->randomElement(['wait', 'done']),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
}
