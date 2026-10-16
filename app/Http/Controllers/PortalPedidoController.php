<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PortalPedidoController extends Controller
{
    public function catalogo(): Response
    {
        $repuestos = Repuesto::query()
            ->where('cantidad', '>', 0)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'precio', 'cantidad']);

        return Inertia::render('portal/Catalogo', ['repuestos' => $repuestos]);
    }

    public function index(Request $request): Response
    {
        $pedidos = Pedido::query()
            ->where('cliente_id', $request->user()->cliente_id)
            ->with('detalles.repuesto:id,nombre')
            ->latest()
            ->get();

        return Inertia::render('portal/Pedidos', ['pedidos' => $pedidos]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.repuesto_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('repuestos', 'id')->whereNull('deleted_at'),
            ],
            'items.*.cantidad' => ['required', 'integer', 'min:1', 'max:999'],
            'nota' => ['nullable', 'string', 'max:500'],
        ]);

        $repuestos = Repuesto::query()
            ->whereIn('id', $request->collect('items')->pluck('repuesto_id')->all())
            ->get()
            ->keyBy('id');

        foreach ($datos['items'] as $item) {
            $repuesto = $repuestos[$item['repuesto_id']];

            if ($item['cantidad'] > $repuesto->cantidad) {
                return back()->withErrors([
                    'items' => "No hay suficiente stock de {$repuesto->nombre}. Disponible: {$repuesto->cantidad}.",
                ]);
            }
        }

        DB::transaction(function () use ($request, $datos, $repuestos) {
            $pedido = Pedido::create([
                'cliente_id' => $request->user()->cliente_id,
                'estado' => 'pendiente',
                'nota' => $datos['nota'] ?? null,
            ]);

            foreach ($datos['items'] as $item) {
                $pedido->detalles()->create([
                    'repuesto_id' => $item['repuesto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $repuestos[$item['repuesto_id']]->precio,
                ]);
            }
        });

        return redirect('/portal/pedidos')->with('success', 'Tu pedido fue enviado. El taller lo revisará pronto.');
    }
}
