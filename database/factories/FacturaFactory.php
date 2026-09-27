<?php

namespace Database\Factories;

use App\Models\Factura;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacturaFactory extends Factory
{
    protected $model = Factura::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50000, 500000);

        return [
            'servicio_id' => Servicio::factory(),
            'subtotal' => $subtotal,
            'descuento' => 0,
            'total' => $subtotal,
            'estado' => 'pendiente',
        ];
    }
}
