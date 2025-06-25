<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 10; $i++) {
                $userId = fake()->numberBetween(1, 10);
                $followId = fake()->numberBetween(1, 10);
                while ($followId == $userId) {
                    $followId = fake()->numberBetween(1, 10);
                }
                DB::table('follows')->insert([
                    'user_id' => $userId,
                    'follow_id' => $followId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
}
