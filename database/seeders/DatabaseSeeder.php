<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPicture;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 1000 users
        $users = User::factory(1000)->create();

        // Create 3 pictures for each user
        $users->each(function ($user) {
            for ($i = 1; $i <= 3; $i++) {
                UserPicture::factory()->create([
                    'user_id' => $user->id,
                    'sort_order' => $i,
                ]);
            }
        });
    }
}
