<?php

namespace Database\Factories;

use App\Models\Repuesto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Repuesto>
 */
class RepuestoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(),
            'precio' => $this->faker->randomFloat(2, 5000, 300000),
            'cantidad' => $this->faker->numberBetween(0, 50),
            'cantidad_minima' => $this->faker->numberBetween(3, 10),
        ];
    }
}
