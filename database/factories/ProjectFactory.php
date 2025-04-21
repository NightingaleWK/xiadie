<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $projectTypes = ['智慧交通', '智慧环保', '天网工程', '平安城市', '智慧城市', '数据中心'];
        $startDate = fake()->dateTimeBetween('-3 years', '+6 months');
        $operationDate = fake()->optional(0.5)->dateTimeBetween($startDate, '+2 months');
        $endDate = fake()->optional(0.3)->dateTimeBetween($operationDate ?? $startDate, '+1 year');

        $statusOptions = [
            'planning' => 30,
            'in_progress' => 40,
            'operation' => 20,
            'completed' => 10,
        ];

        return [
            'name' => fake()->randomElement($projectTypes) . fake()->words(2, true),
            'name_en' => fake()->optional(0.7)->sentence(3),
            'code' => 'PRJ-' . strtoupper(fake()->unique()->lexify('??')) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraph(),
            'start_date' => $startDate,
            'operation_date' => $operationDate,
            'end_date' => $endDate,
            'project_manager' => fake()->name(),
            'manager_phone' => fake()->phoneNumber(),
            'client_name' => fake()->company(),
            'client_contact' => fake()->name(),
            'client_phone' => fake()->phoneNumber(),
            'status' => fake()->randomElement(
                array_keys($statusOptions)
            ),
            'remarks' => fake()->optional(0.5)->paragraph(),
        ];
    }
}
