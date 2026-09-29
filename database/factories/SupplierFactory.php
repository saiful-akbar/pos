<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    #[Override]
    public function modelName()
    {
        return Supplier::class;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => str_random(8),
            'name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'notes' => 'Seeder factory',
        ];
    }
}
