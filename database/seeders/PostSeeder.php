<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 10; $i++) {
                DB::table('posts')->insert([
                    'user_id' => fake()->numberBetween(1, 10),
                    'content' => fake()->text(50),
                    'view_count' => fake()->numberBetween(1000, 10000),
                    'timeline_orders' => now(),
                    'images' => fake()->imageUrl(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
