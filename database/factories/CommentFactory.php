<?php

namespace Database\Factories;

use App\Models\Comment;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'task_id' => random_int(1, 30),
            'user_id' => random_int(1, 30),
            'body' => fake()->paragraph(),
        ];
    }
}
