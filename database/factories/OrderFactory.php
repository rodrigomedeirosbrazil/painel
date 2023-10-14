<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'description' => $this->faker->text(),
            'customer_id' => function () {
                return Customer::factory()->create()->id;
            },
            'pickup' => $this->faker->date(),
            'delivery' => $this->faker->date(),
            'deposit' => $this->faker->randomFloat(2, 0, 9999999999.99),
            'discount' => $this->faker->randomFloat(2, 0, 9999999999.99),
            'amount' => $this->faker->randomFloat(2, 0, 9999999999.99),
        ];
    }
}
