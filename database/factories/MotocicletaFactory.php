<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class MotocicletaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'placa' => strtoupper(fake()->unique()->bothify('???###')),
            'marca' => fake()->randomElement(['Honda', 'Yamaha', 'Suzuki', 'AKT', 'Bajaj']),
            'modelo' => fake()->word(),
            'anio' => fake()->numberBetween(2000, 2026),
            'cilindraje' => fake()->randomElement([110, 125, 150, 200]),
            'color' => fake()->safeColorName(),
        ];
    }
}
