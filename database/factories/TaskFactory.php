<?php

namespace Database\Factories;

use App\Models\Status;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'project_id' => 1,
            'tag_id' => random_int(1, 6),
            'status_id' => random_int(1, 3),
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraphs(6, true),
            'priority' => fake()->boolean(),
            'due_date' => fake()->dateTimeBetween('now', '+1 year'),
        ];
    }
}
