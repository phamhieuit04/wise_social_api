<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private $names = [
        'Hieu Pham',
        'Minh Ngoc'
    ];

    private $emails = [
        'tomnguyenhieu20044@gmail.com',
        'feyd153@gmail.com'
    ];

    private $overviews = [
        'Dev Laravel, Vue...',
        'Dev Vue, Laravel...'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            DB::table('users')->insert([
                'name' => fake()->name(),
                'email' => fake()->unique()->email(),
                'password' => Hash::make('12345678'),
                'location' => 'Viet Nam',
                'city' => 'Ha Noi',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            User::create([
                'name' => $this->names[$i],
                'email' => $this->emails[$i],
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'overview' => $this->overviews[$i],
                'location' => 'Viet Nam',
                'city' => 'Ha Noi',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
