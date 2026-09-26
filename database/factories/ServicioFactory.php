<?php

namespace Database\Factories;

use App\Models\Motocicleta;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Servicio>
 */
class ServicioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'motocicleta_id' => Motocicleta::factory(),
            'mecanico_id' => null,
            'descripcion_problema' => $this->faker->sentence(),
            'estado' => 'pendiente',
            'costo_mano_obra' => $this->faker->randomFloat(2, 20000, 100000),
            'costo_total' => 0,
            'fecha_ingreso' => now(),
        ];
    }
}
