<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KPIEntry>
 */
class KPIEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $codeNumber = 1;

        $quantity = $this->faker->numberBetween(1, 1000);
        $asp = $this->faker->randomFloat(2, 10, 1000);
        $totalValue = $quantity * $asp;

        return [
            'code' => 'KPI-' . str_pad($codeNumber++, 4, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'product_id' => Product::factory(),
            'supplier_id' => Supplier::factory(),
            'month' => $this->faker->date('Y-m'),
            'uom' => $this->faker->randomElement(['pcs', 'kg', 'litre', 'box']),
            'quantity' => $quantity,
            'asp' => $asp,
            'total_value' => $totalValue,
        ];
    }
}
