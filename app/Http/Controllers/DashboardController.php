<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private const ESTADOS = ['pendiente', 'en_proceso', 'terminado', 'entregado'];

    private const MESES = [
        'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
        'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic',
    ];

    public function index(Request $request): Response
    {
        $usuario = $request->user();
        $soloMecanico = $usuario->esMecanico()
            && ! $usuario->esAdmin()
            && ! $usuario->esRecepcionista();

        // Un mecánico solo ve sus propios servicios
        $servicios = Servicio::query()
            ->when($soloMecanico, fn ($q) => $q->where('mecanico_id', $usuario->id));

        $conteo = (clone $servicios)
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estados = collect(self::ESTADOS)->map(fn (string $estado) => [
            'estado' => $estado,
            'total' => (int) $conteo->get($estado, 0),
        ])->values();

        // Servicios creados en los últimos 6 meses
        $desde = now()->startOfMonth()->subMonths(5);
        $porMes = (clone $servicios)
            ->where('created_at', '>=', $desde)
            ->pluck('created_at')
            ->countBy(fn ($fecha) => $fecha->format('Y-m'));

        $meses = collect(range(5, 0))->map(function (int $atras) use ($porMes) {
            $mes = now()->startOfMonth()->subMonths($atras);

            return [
                'etiqueta' => self::MESES[$mes->month - 1],
                'total' => (int) $porMes->get($mes->format('Y-m'), 0),
            ];
        })->values();

        $recientes = (clone $servicios)
            ->with(['motocicleta.cliente', 'mecanico'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Servicio $servicio) => [
                'id' => $servicio->id,
                'placa' => $servicio->motocicleta->placa,
                'cliente' => $servicio->motocicleta->cliente->nombre.' '.$servicio->motocicleta->cliente->apellido,
                'estado' => $servicio->estado,
                'costo_total' => (float) $servicio->costo_total,
            ]);

        $datos = [
            'solo_mecanico' => $soloMecanico,
            'estados' => $estados,
            'meses' => $meses,
            'recientes' => $recientes,
            'ingresos_mes' => null,
            'facturas_pendientes' => null,
            'total_bajo_stock' => null,
            'bajo_stock' => [],
            'mecanicos' => [],
        ];

        if (! $soloMecanico) {
            $datos['ingresos_mes'] = (float) Factura::where('estado', 'pagada')
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('total');

            $datos['facturas_pendientes'] = Factura::where('estado', 'pendiente')->count();

            $bajoStock = Repuesto::query()->whereColumn('cantidad', '<=', 'cantidad_minima');
            $datos['total_bajo_stock'] = (clone $bajoStock)->count();
            $datos['bajo_stock'] = $bajoStock
                ->orderBy('cantidad')
                ->get(['id', 'nombre', 'cantidad', 'cantidad_minima']);

            // Disponibilidad: servicios activos por mecánico
            $carga = Servicio::query()
                ->whereIn('estado', ['pendiente', 'en_proceso'])
                ->whereNotNull('mecanico_id')
                ->selectRaw('mecanico_id, count(*) as total')
                ->groupBy('mecanico_id')
                ->pluck('total', 'mecanico_id');

            $datos['mecanicos'] = User::query()
                ->where('rol', 'mecanico')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (User $mecanico) => [
                    'id' => $mecanico->id,
                    'name' => $mecanico->name,
                    'activos' => (int) $carga->get($mecanico->id, 0),
                ]);
        }

        return Inertia::render('Dashboard', $datos);
    }
}
