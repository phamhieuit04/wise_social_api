<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FriendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 10; $i++) {
                $userId = fake()->numberBetween(1, 10);
                $friendId = fake()->numberBetween(1, 10);
                while ($friendId == $userId) {
                    $friendId = fake()->numberBetween(1, 10);
                }
                DB::table('friends')->insert([
                    'user_id' => $userId,
                    'friend_id' => $friendId,
                    'approved' => fake()->numberBetween(1, 2),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
}
