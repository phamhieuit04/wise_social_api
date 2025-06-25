<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ExperienceSeeder::class,
            SkillSeeder::class,
            PostSeeder::class,
            LikeSeeder::class,
            FavoriteSeeder::class,
            FollowSeeder::class,
            FriendSeeder::class,
            ChatRoomSeeder::class,
            ViolenceWarningSeeder::class
            // TODO: add notifications, user_views, violence_logs, messages, comments seeder
        ]);
    }
}
