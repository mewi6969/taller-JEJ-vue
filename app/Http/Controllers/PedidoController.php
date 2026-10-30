<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PedidoController extends Controller
{
    public function index(Request $request): Response
    {
        $this->soloAdminORecepcion($request);

        $pedidos = Pedido::query()
            ->with([
                'cliente:id,nombre,apellido,telefono',
                'motocicleta:id,placa,marca,modelo',
                'detalles.repuesto:id,nombre',
            ])
            ->latest()
            ->get();

        return Inertia::render('pedidos/Index', ['pedidos' => $pedidos]);
    }

    public function update(Request $request, Pedido $pedido): RedirectResponse
    {
        $this->soloAdminORecepcion($request);

        $datos = $request->validate([
            'estado' => ['required', Rule::in(['pendiente', 'atendido', 'cancelado'])],
        ]);

        $pedido->update($datos);

        return back()->with('success', 'Pedido actualizado.');
    }

    private function soloAdminORecepcion(Request $request): void
    {
        abort_unless(in_array($request->user()->rol, ['admin', 'recepcionista'], true), 403);
    }
}
