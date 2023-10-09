<?php

namespace Database\Factories;

use App\Models\Label;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LabelTask>
 */
class LabelTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'label_id' => Label::get()->random()->id,
            'task_id' => Task::get()->random()->id,
        ];
    }
}
