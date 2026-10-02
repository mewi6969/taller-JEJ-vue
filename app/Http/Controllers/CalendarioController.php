<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarioController extends Controller
{
    public function index(Request $request): Response
    {
        $usuario = $request->user();
        $soloMecanico = $usuario->esMecanico()
            && ! $usuario->esAdmin()
            && ! $usuario->esRecepcionista();

        $servicios = Servicio::query()
            ->with(['motocicleta.cliente', 'mecanico'])
            ->whereNotNull('fecha_ingreso')
            ->where('fecha_ingreso', '>=', now()->subYear()->toDateString())
            ->when($soloMecanico, fn ($q) => $q->where('mecanico_id', $usuario->id))
            ->orderBy('fecha_ingreso')
            ->get()
            ->map(function (Servicio $servicio) {
                $moto = $servicio->motocicleta;
                $cliente = $moto?->cliente;

                $inicio = $servicio->fecha_ingreso;
                $fin = $servicio->fecha_entrega && $servicio->fecha_entrega->gte($inicio)
                    ? $servicio->fecha_entrega
                    : $inicio;

                return [
                    'id' => $servicio->id,
                    'placa' => $moto->placa ?? 'Sin placa',
                    'cliente' => $cliente
                        ? $cliente->nombre.' '.$cliente->apellido
                        : 'Cliente eliminado',
                    'mecanico' => $servicio->mecanico?->name,
                    'estado' => $servicio->estado,
                    'inicio' => $inicio->toDateString(),
                    // FullCalendar no pinta el último día, por eso se suma uno
                    'fin' => $fin->copy()->addDay()->toDateString(),
                ];
            })
            ->values();

        return Inertia::render('Calendario', [
            'servicios' => $servicios,
        ]);
    }
}
