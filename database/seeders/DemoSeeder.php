<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\DetalleServicio;
use App\Models\Factura;
use App\Models\Motocicleta;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $mecanicos = $this->crearUsuarios();
        $motos = $this->crearClientesYMotos();
        $repuestos = $this->crearRepuestos();

        $this->crearServicios($mecanicos, $motos, $repuestos);
        $this->dejarRepuestosEnBajoStock();
    }

    /**
     * @return array<int, User>
     */
    private function crearUsuarios(): array
    {
        User::factory()->create([
            'name' => 'Laura Gómez',
            'email' => 'recepcion@tallermotos.com',
            'password' => '12345678',
            'rol' => 'recepcionista',
        ]);

        return [
            User::factory()->create([
                'name' => 'Carlos Ramírez',
                'email' => 'carlos@tallermotos.com',
                'password' => '12345678',
                'rol' => 'mecanico',
            ]),
            User::factory()->create([
                'name' => 'Andrés Torres',
                'email' => 'andres@tallermotos.com',
                'password' => '12345678',
                'rol' => 'mecanico',
                'puede_crear_servicios' => true,
            ]),
        ];
    }

    /**
     * @return array<int, Motocicleta>
     */
    private function crearClientesYMotos(): array
    {
        $datosClientes = [
            ['Jorge', 'Mejía', '1061234501', '3105550142', 'jorge.mejia@example.com', 'Calle 10 # 5-32'],
            ['Marcela', 'Ortiz', '1061234502', '3115550173', 'marcela.ortiz@example.com', 'Carrera 7 # 12-45'],
            ['Sebastián', 'Cruz', '1061234503', '3125550194', 'sebastian.cruz@example.com', 'Calle 4 # 9-18'],
            ['Valentina', 'Rojas', '1061234504', '3135550115', null, 'Carrera 3 # 6-27'],
            ['Diego', 'Martínez', '1061234505', '3145550136', 'diego.martinez@example.com', 'Calle 15 # 2-60'],
            ['Camila', 'Herrera', '1061234506', '3155550157', 'camila.herrera@example.com', 'Carrera 9 # 11-14'],
            ['Felipe', 'Castro', '1061234507', '3165550178', 'felipe.castro@example.com', 'Calle 8 # 7-39'],
            ['Daniela', 'Vargas', '1061234508', '3175550199', 'daniela.vargas@example.com', 'Carrera 5 # 3-52'],
        ];

        $clientes = [];

        foreach ($datosClientes as [$nombre, $apellido, $documento, $telefono, $email, $direccion]) {
            $clientes[] = Cliente::create([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'documento' => $documento,
                'telefono' => $telefono,
                'email' => $email,
                'direccion' => $direccion,
            ]);
        }

        // [índice del cliente, placa, marca, modelo, año, cilindraje, color]
        $datosMotos = [
            [0, 'KLM41F', 'Yamaha', 'FZ 2.0', 2021, 150, 'Negro'],
            [1, 'RTX92B', 'Honda', 'CB 190R', 2022, 190, 'Rojo'],
            [2, 'HNB15D', 'Suzuki', 'Gixxer 150', 2020, 150, 'Azul'],
            [3, 'PQS37A', 'Bajaj', 'Pulsar NS 160', 2023, 160, 'Negro'],
            [4, 'WDR68C', 'AKT', 'NKD 125', 2019, 125, 'Blanco'],
            [5, 'FGH24E', 'TVS', 'Apache RTR 160', 2021, 160, 'Gris'],
            [6, 'ZXC83K', 'Yamaha', 'XTZ 125', 2018, 125, 'Azul'],
            [7, 'MNB56L', 'Honda', 'XR 150L', 2022, 150, 'Rojo'],
            [0, 'TYU19P', 'Bajaj', 'Boxer CT 100', 2020, 100, 'Negro'],
            [1, 'LKJ72R', 'Hero', 'Hunk 160R', 2024, 160, 'Gris'],
        ];

        $motos = [];

        foreach ($datosMotos as [$indice, $placa, $marca, $modelo, $anio, $cilindraje, $color]) {
            $motos[] = Motocicleta::create([
                'cliente_id' => $clientes[$indice]->id,
                'placa' => $placa,
                'marca' => $marca,
                'modelo' => $modelo,
                'anio' => $anio,
                'cilindraje' => $cilindraje,
                'color' => $color,
            ]);
        }

        return $motos;
    }

    /**
     * @return array<string, Repuesto>
     */
    private function crearRepuestos(): array
    {
        // [nombre, descripción, precio, cantidad inicial, cantidad mínima]
        $datos = [
            ['Filtro de aceite', 'Filtro de aceite para motos de 100 a 200 cc', 18000, 60, 10],
            ['Aceite 20W-50', 'Aceite semisintético, presentación de 1 litro', 32000, 60, 12],
            ['Pastillas de freno delanteras', 'Juego de pastillas delanteras', 45000, 40, 8],
            ['Pastillas de freno traseras', 'Juego de pastillas traseras', 38000, 40, 8],
            ['Kit de arrastre', 'Cadena y piñones', 120000, 25, 3],
            ['Bujía NGK', 'Bujía estándar para motos de baja cilindrada', 15000, 60, 10],
            ['Batería 12V', 'Batería sellada libre de mantenimiento', 150000, 20, 4],
            ['Llanta trasera', 'Llanta trasera para moto de 150 a 200 cc', 180000, 12, 3],
            ['Cable de clutch', 'Cable de embrague universal', 22000, 30, 6],
            ['Bombillo faro LED', 'Bombillo LED para faro delantero', 35000, 30, 6],
            ['Filtro de aire', 'Filtro de aire de espuma', 28000, 40, 6],
            ['Líquido de frenos', 'Líquido de frenos DOT 4, 250 ml', 18000, 40, 8],
            ['Espejo retrovisor', 'Espejo retrovisor universal', 25000, 30, 6],
            ['Amortiguador trasero', 'Amortiguador trasero con resorte', 210000, 10, 2],
        ];

        $repuestos = [];

        foreach ($datos as [$nombre, $descripcion, $precio, $cantidad, $minima]) {
            $repuestos[$nombre] = Repuesto::create([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'cantidad' => $cantidad,
                'cantidad_minima' => $minima,
            ]);
        }

        return $repuestos;
    }

    /**
     * @param  array<int, User>  $mecanicos
     * @param  array<int, Motocicleta>  $motos
     * @param  array<string, Repuesto>  $repuestos
     */
    private function crearServicios(array $mecanicos, array $motos, array $repuestos): void
    {
        foreach ($this->servicios() as $datos) {
            $ingreso = now()->subDays($datos['ingreso'])->startOfDay();
            $entrega = $datos['entrega'] === null
                ? null
                : now()->subDays($datos['entrega'])->startOfDay();
            $creado = $ingreso->copy()->setTime(9, 0);

            $servicio = new Servicio;
            $servicio->forceFill([
                'motocicleta_id' => $motos[$datos['moto']]->id,
                'mecanico_id' => $datos['mecanico'] === null
                    ? null
                    : $mecanicos[$datos['mecanico']]->id,
                'descripcion_problema' => $datos['problema'],
                'estado' => $datos['estado'],
                'costo_mano_obra' => $datos['mano_obra'],
                'costo_total' => $datos['mano_obra'],
                'fecha_ingreso' => $ingreso,
                'fecha_entrega' => $entrega,
                'created_at' => $creado,
                'updated_at' => $creado,
            ])->save();

            // Al crear cada detalle, el modelo descuenta el stock
            // y recalcula el costo total del servicio por su cuenta.
            foreach ($datos['repuestos'] as $nombre => $cantidad) {
                $repuesto = $repuestos[$nombre];

                DetalleServicio::create([
                    'servicio_id' => $servicio->id,
                    'repuesto_id' => $repuesto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $repuesto->precio,
                ]);
            }

            $servicio->refresh();

            if ($datos['factura'] !== null) {
                $this->crearFactura($servicio, $datos['factura']);
            }
        }
    }

    /**
     * @param  array{estado: string, metodo?: string, hace: int, descuento?: int}  $datos
     */
    private function crearFactura(Servicio $servicio, array $datos): void
    {
        $descuento = $datos['descuento'] ?? 0;
        $fecha = now()->subDays($datos['hace']);
        $pagada = $datos['estado'] === 'pagada';

        // El número (FAC-000001...) lo asigna el modelo Factura al crearla
        (new Factura)->forceFill([
            'servicio_id' => $servicio->id,
            'subtotal' => $servicio->costo_total,
            'descuento' => $descuento,
            'total' => $servicio->costo_total - $descuento,
            'estado' => $datos['estado'],
            'metodo_pago' => $pagada ? ($datos['metodo'] ?? null) : null,
            'fecha_pago' => $pagada ? $fecha->toDateString() : null,
            'created_at' => $fecha,
            'updated_at' => $fecha,
        ])->save();
    }

    /**
     * Cada fecha se cuenta en "días atrás" desde hoy; un número negativo
     * es una fecha futura (entrega estimada).
     *
     * @return array<int, array<string, mixed>>
     */
    private function servicios(): array
    {
        return [
            [
                'moto' => 0, 'mecanico' => 0, 'problema' => 'Cambio de aceite y filtro',
                'estado' => 'entregado', 'mano_obra' => 35000, 'ingreso' => 155, 'entrega' => 154,
                'repuestos' => ['Filtro de aceite' => 1, 'Aceite 20W-50' => 2],
                'factura' => ['estado' => 'pagada', 'metodo' => 'efectivo', 'hace' => 154],
            ],
            [
                'moto' => 1, 'mecanico' => 1, 'problema' => 'Frenos con ruido al frenar',
                'estado' => 'entregado', 'mano_obra' => 40000, 'ingreso' => 140, 'entrega' => 138,
                'repuestos' => ['Pastillas de freno delanteras' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'transferencia', 'hace' => 138],
            ],
            [
                'moto' => 2, 'mecanico' => 0, 'problema' => 'Kit de arrastre desgastado',
                'estado' => 'entregado', 'mano_obra' => 60000, 'ingreso' => 125, 'entrega' => 123,
                'repuestos' => ['Kit de arrastre' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'tarjeta', 'hace' => 123],
            ],
            [
                'moto' => 3, 'mecanico' => 1, 'problema' => 'No enciende, batería descargada',
                'estado' => 'entregado', 'mano_obra' => 30000, 'ingreso' => 112, 'entrega' => 111,
                'repuestos' => ['Batería 12V' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'efectivo', 'hace' => 111],
            ],
            [
                'moto' => 4, 'mecanico' => 0, 'problema' => 'Mantenimiento de 5.000 km',
                'estado' => 'entregado', 'mano_obra' => 55000, 'ingreso' => 98, 'entrega' => 96,
                'repuestos' => ['Filtro de aceite' => 1, 'Aceite 20W-50' => 2, 'Bujía NGK' => 1, 'Filtro de aire' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'transferencia', 'hace' => 96, 'descuento' => 5000],
            ],
            [
                'moto' => 5, 'mecanico' => 1, 'problema' => 'Cambio de llanta trasera',
                'estado' => 'entregado', 'mano_obra' => 25000, 'ingreso' => 85, 'entrega' => 84,
                'repuestos' => ['Llanta trasera' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'tarjeta', 'hace' => 84],
            ],
            [
                'moto' => 6, 'mecanico' => 0, 'problema' => 'Revisión del sistema eléctrico',
                'estado' => 'entregado', 'mano_obra' => 45000, 'ingreso' => 70, 'entrega' => 67,
                'repuestos' => ['Bombillo faro LED' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'efectivo', 'hace' => 67],
            ],
            [
                'moto' => 7, 'mecanico' => 1, 'problema' => 'Cable de clutch roto',
                'estado' => 'entregado', 'mano_obra' => 20000, 'ingreso' => 58, 'entrega' => 57,
                'repuestos' => ['Cable de clutch' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'efectivo', 'hace' => 57],
            ],
            [
                'moto' => 8, 'mecanico' => 0, 'problema' => 'Cambio de amortiguador trasero',
                'estado' => 'entregado', 'mano_obra' => 70000, 'ingreso' => 46, 'entrega' => 44,
                'repuestos' => ['Amortiguador trasero' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'transferencia', 'hace' => 44],
            ],
            [
                'moto' => 9, 'mecanico' => 1, 'problema' => 'Afinación general',
                'estado' => 'entregado', 'mano_obra' => 80000, 'ingreso' => 35, 'entrega' => 32,
                'repuestos' => ['Bujía NGK' => 1, 'Filtro de aire' => 1, 'Filtro de aceite' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'tarjeta', 'hace' => 32, 'descuento' => 10000],
            ],
            [
                'moto' => 0, 'mecanico' => 1, 'problema' => 'Frenos traseros gastados',
                'estado' => 'entregado', 'mano_obra' => 30000, 'ingreso' => 26, 'entrega' => 25,
                'repuestos' => ['Pastillas de freno traseras' => 1],
                'factura' => ['estado' => 'pendiente', 'hace' => 25],
            ],
            [
                'moto' => 1, 'mecanico' => 0, 'problema' => 'Cambio de aceite',
                'estado' => 'entregado', 'mano_obra' => 35000, 'ingreso' => 18, 'entrega' => 17,
                'repuestos' => ['Filtro de aceite' => 1, 'Aceite 20W-50' => 2],
                'factura' => ['estado' => 'pagada', 'metodo' => 'efectivo', 'hace' => 17],
            ],
            [
                'moto' => 2, 'mecanico' => 1, 'problema' => 'Revisión de frenos y cambio de líquido',
                'estado' => 'entregado', 'mano_obra' => 40000, 'ingreso' => 11, 'entrega' => 10,
                'repuestos' => ['Líquido de frenos' => 1],
                'factura' => ['estado' => 'pagada', 'metodo' => 'tarjeta', 'hace' => 10],
            ],
            [
                'moto' => 3, 'mecanico' => 0, 'problema' => 'Cambio de espejos retrovisores',
                'estado' => 'entregado', 'mano_obra' => 15000, 'ingreso' => 6, 'entrega' => 5,
                'repuestos' => ['Espejo retrovisor' => 2],
                'factura' => ['estado' => 'pagada', 'metodo' => 'efectivo', 'hace' => 0],
            ],
            [
                'moto' => 4, 'mecanico' => 1, 'problema' => 'Cambio de kit de arrastre',
                'estado' => 'terminado', 'mano_obra' => 60000, 'ingreso' => 5, 'entrega' => 2,
                'repuestos' => ['Kit de arrastre' => 1],
                'factura' => null,
            ],
            [
                'moto' => 5, 'mecanico' => 0, 'problema' => 'Cambio de bujías y filtro de aire',
                'estado' => 'terminado', 'mano_obra' => 40000, 'ingreso' => 4, 'entrega' => 1,
                'repuestos' => ['Bujía NGK' => 2, 'Filtro de aire' => 1],
                'factura' => ['estado' => 'pendiente', 'hace' => 1],
            ],
            [
                'moto' => 6, 'mecanico' => 0, 'problema' => 'Pierde potencia al acelerar',
                'estado' => 'en_proceso', 'mano_obra' => 50000, 'ingreso' => 3, 'entrega' => -2,
                'repuestos' => ['Bujía NGK' => 1],
                'factura' => null,
            ],
            [
                'moto' => 7, 'mecanico' => 1, 'problema' => 'Cambio de batería',
                'estado' => 'en_proceso', 'mano_obra' => 25000, 'ingreso' => 2, 'entrega' => -1,
                'repuestos' => ['Batería 12V' => 1],
                'factura' => null,
            ],
            [
                'moto' => 8, 'mecanico' => 1, 'problema' => 'Ruido en la suspensión delantera',
                'estado' => 'en_proceso', 'mano_obra' => 40000, 'ingreso' => 1, 'entrega' => -3,
                'repuestos' => [],
                'factura' => null,
            ],
            [
                'moto' => 9, 'mecanico' => null, 'problema' => 'Revisión general antes de viaje',
                'estado' => 'pendiente', 'mano_obra' => 60000, 'ingreso' => 0, 'entrega' => -3,
                'repuestos' => [],
                'factura' => null,
            ],
            [
                'moto' => 0, 'mecanico' => 0, 'problema' => 'La luz delantera no enciende',
                'estado' => 'pendiente', 'mano_obra' => 20000, 'ingreso' => 0, 'entrega' => -1,
                'repuestos' => [],
                'factura' => null,
            ],
            [
                'moto' => 1, 'mecanico' => null, 'problema' => 'Ruido en la cadena',
                'estado' => 'pendiente', 'mano_obra' => 25000, 'ingreso' => 1, 'entrega' => -2,
                'repuestos' => [],
                'factura' => null,
            ],
        ];
    }

    /**
     * Deja algunos repuestos en o por debajo de su mínimo, para que el
     * dashboard muestre el aviso de bajo stock y el botón "Ver todos".
     */
    private function dejarRepuestosEnBajoStock(): void
    {
        $bajos = [
            'Batería 12V' => 2,
            'Kit de arrastre' => 1,
            'Pastillas de freno delanteras' => 5,
            'Bujía NGK' => 10,
            'Aceite 20W-50' => 9,
            'Filtro de aire' => 3,
        ];

        foreach ($bajos as $nombre => $cantidad) {
            Repuesto::where('nombre', $nombre)->update(['cantidad' => $cantidad]);
        }
    }
}
