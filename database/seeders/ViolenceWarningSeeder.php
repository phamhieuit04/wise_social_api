<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViolenceWarningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 4; $i++) {
                DB::table('violence_warnings')->insert([
                    'user_id' => $i + 1,
                    'infringe' => fake()->numberBetween(1, 5),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
}
