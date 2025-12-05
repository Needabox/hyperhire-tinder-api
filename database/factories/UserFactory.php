<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'device_id' => fake()->unique()->uuid(),
            'name' => fake()->name(),
            'age' => fake()->numberBetween(18, 65),
            'longitude' => fake()->longitude(),
            'latitude' => fake()->latitude(),
            'remember_token' => Str::random(10),
        ];
    }
}
