<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'code' => 'PROD-' . str_pad($codeNumber++, 4, '0', STR_PAD_LEFT),
            'name' => $this->faker->word(),
            'description' => $this->faker->optional()->sentence(),
            'uom' => $this->faker->randomElement(['pcs', 'kg', 'litre', 'meter', 'box']),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            $suppliers = Supplier::inRandomOrder()->take(rand(1, 3))->pluck('id');

            if ($suppliers->isEmpty()) {
                $suppliers = Supplier::factory()->count(3)->create()->pluck('id');
            }

            $product->suppliers()->attach($suppliers);
        });
    }
}
