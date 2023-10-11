<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'value' => $this->faker->randomFloat(2, 0, 1000),
            'value_repo' => $this->faker->randomFloat(2, 0, 1000),
            'quantity' => $this->faker->numberBetween(0, 100),
            'width' => $this->faker->numberBetween(0, 100),
            'height' => $this->faker->numberBetween(0, 100),
            'length' => $this->faker->numberBetween(0, 100),
        ];
    }
}
