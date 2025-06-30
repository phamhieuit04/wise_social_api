<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViolenceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 4; $i++) {
                DB::table('violence_logs')->insert([
                    'message_id' => fake()->numberBetween(1, 10),
                    'post_id' => fake()->numberBetween(1, 10),
                    'comment_id' => fake()->numberBetween(1, 10),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
}
