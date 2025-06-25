<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 10; $i++) {
                $user1 = fake()->numberBetween(1, 10);
                $user2 = fake()->numberBetween(1, 10);
                while ($user2 == $user1) {
                    $user2 = fake()->numberBetween(1, 10);
                }
                DB::table('chat_rooms')->insert([
                    'user_id' => "[$user1, $user2]",
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
}
