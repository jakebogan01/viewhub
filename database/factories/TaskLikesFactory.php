<?php

namespace Database\Factories;

use App\Models\TaskLikes;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskLikes>
 */
class TaskLikesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => random_int(1, 30),
            'task_id' => random_int(1, 30),
        ];
    }
}
