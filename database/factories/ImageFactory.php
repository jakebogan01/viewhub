<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomImages = [
            'https://picsum.photos/600',
            'https://picsum.photos/600',
            'https://picsum.photos/800',
            'https://picsum.photos/700',
            'https://picsum.photos/500',
            'https://picsum.photos/400',
        ];

        return [
            'task_id' => fake()->numberBetween(1, 30),
            'name' => fake()->word(),
            'path' => $randomImages[array_rand($randomImages)],
        ];
    }
}
