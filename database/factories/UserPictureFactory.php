<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPicture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPicture>
 */
class UserPictureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageServices = [
            'https://picsum.photos/640/480?random=' . fake()->numberBetween(1, 1000),
            'https://i.pravatar.cc/640?img=' . fake()->numberBetween(1, 70),
            'https://randomuser.me/api/portraits/' . fake()->randomElement(['men', 'women']) . '/' . fake()->numberBetween(1, 99) . '.jpg',
            fake()->imageUrl(640, 480, 'people', true),
        ];

        return [
            'user_id' => User::factory(),
            'picture_url' => fake()->randomElement($imageServices),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}

