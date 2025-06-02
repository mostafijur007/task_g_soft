<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
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
            'code' => 'SUP-' . str_pad($codeNumber++, 4, '0', STR_PAD_LEFT),
            'name' => $this->faker->company(),
            'email' =>  $this->faker->optional()->email(),
            'phone' => $this->faker->optional()->phoneNumber(),
        ];
    }
}
