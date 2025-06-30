<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 10; $i++) {
                DB::table('messages')->insert([
                    'user_id' => fake()->numberBetween(1, 10),
                    'room_id' => fake()->numberBetween(1, 10),
                    'message' => fake()->sentence(),
                    'is_view' => fake()->numberBetween(1, 2),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
}
