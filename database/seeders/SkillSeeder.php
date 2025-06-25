<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 10; $i++) {
                DB::table('skills')->insert([
                    'user_id' => fake()->numberBetween(1, 10),
                    'skill' => fake()->randomElement(
                        ["Laravel, VueJS", "NodeJS, React", "Flutter, React Native"]
                    ),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
}
