<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            for ($i = 0; $i < 10; $i++) {
                DB::table('users')->insert([
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'password' => Hash::make('12345678'),
                    'location' => fake()->country(),
                    'city' => fake()->city(),
                    'banner' => fake()->sentence(4),
                    'overview' => fake()->sentence(),
                    'online_status' => fake()->numberBetween(1, 2),
                    'status' => fake()->numberBetween(0, 1),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }
}
