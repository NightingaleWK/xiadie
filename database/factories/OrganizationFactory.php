<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'description' => $this->faker->paragraph(),
            'is_active' => $this->faker->boolean(90), // 90% 概率为活跃状态
        ];
    }

    /**
     * 指定组织为顶级组织
     */
    public function root(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => null,
                'level' => 0,
                'path' => ',',
            ];
        });
    }

    /**
     * 指定组织的父组织
     */
    public function childOf(Organization $parent): Factory
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'parent_id' => $parent->id,
                'level' => $parent->level + 1,
                'path' => $parent->path . $parent->id . ',',
            ];
        });
    }
}
