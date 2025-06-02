<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $codeNumber = 1;
        return [
            'code' => 'CUS-' . str_pad($codeNumber++, 4, '0', STR_PAD_LEFT),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'address' => $this->faker->optional()->address(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Customer $customer) {
            $productIds = Product::inRandomOrder()->take(rand(1, 5))->pluck('id');

            if ($productIds->isEmpty()) {
                $productIds = Product::factory()->count(5)->create()->pluck('id');
            }

            $customer->products()->attach($productIds);
        });
    }
}
