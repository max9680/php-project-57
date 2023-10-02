<?php

namespace Database\Factories;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->word,
            'description' => fake()->sentence(random_int(4, 10)),
            'status_id' => TaskStatus::get()->random()->id,
            'created_by_id' => User::get()->random()->id,
            'assigned_to_id' => User::get()->random()->id,
        ];
    }
}
