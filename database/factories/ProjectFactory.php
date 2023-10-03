<?php

namespace Database\Factories;

use App\Models\Project;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
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
            'company_id' => 1,
            'name' => fake()->unique()->word(),
            'description' => fake()->paragraphs(1, true),
            'duration' => fake()->numberBetween(1, 100),
        ];
    }
}
