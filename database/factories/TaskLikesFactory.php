<?php

namespace Database\Factories;

use App\Models\TaskLikes;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskLikes>
 */
class TaskLikesFactory extends Factory
{
    /**
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'task_id' => 1,
        ];
    }
}
