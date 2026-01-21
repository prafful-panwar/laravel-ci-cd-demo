<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(TaskStatus::cases()),
            'due_date' => $this->faker->boolean() ? $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d') : null,
        ];
    }
}
