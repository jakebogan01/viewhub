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
            'user_id' => User::factory(),
            'tag_id' => random_int(1, 6),
//            'tag_id' => Tag::factory(),
            'status_id' => random_int(1, 10),
//            'status_id' => Status::factory(),
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraphs(6, true),
        ];
    }
}
