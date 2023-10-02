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
            'name' => fake()->sentence(random_int(1, 6)),
            'description' => fake()->sentence(random_int(6, 15)),
            'status_id' => TaskStatus::get()->random()->id,
            'created_by_id' => User::get()->random()->id,
            'assigned_to_id' => User::get()->random()->id,
        ];
    }
}
