<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reply>
 */
class ReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment_id' => random_int(1, 30),
            'user_id' => random_int(1, 30),
            'recipient_id' => random_int(1, 30),
            'body' => fake()->paragraph(),
        ];
    }
}
