<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use App\Models\DetalleServicio;
use App\Models\Motocicleta;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServicioController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Servicio::class);

        $user = request()->user();

        $servicios = Servicio::query()
            ->with(['motocicleta.cliente', 'mecanico'])
            ->when(
                $user->esMecanico() && ! $user->esAdmin() && ! $user->esRecepcionista(),
                fn ($query) => $query->where('mecanico_id', $user->id)
            )
            ->when(request('buscar'), function ($query, $buscar) {
                $query->whereHas('motocicleta', function ($q) use ($buscar) {
                    $q->where('placa', 'like', "%{$buscar}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('servicios/Index', [
            'servicios' => $servicios,
            'filtros' => request()->only('buscar'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Servicio::class);

        return Inertia::render('servicios/Create', [
            'motocicletas' => Motocicleta::with('cliente')->orderBy('placa')->get(),
            'mecanicos' => User::where('rol', 'mecanico')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreServicioRequest $request): RedirectResponse
    {
        Servicio::create($request->validated() + ['estado' => 'pendiente']);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Servicio $servicio): Response
    {
        $this->authorize('update', $servicio);

        $servicio->load(['detalles.repuesto', 'motocicleta.cliente', 'mecanico']);

        return Inertia::render('servicios/Edit', [
            'servicio' => $servicio,
            'motocicletas' => Motocicleta::with('cliente')->orderBy('placa')->get(),
            'mecanicos' => User::where('rol', 'mecanico')->orderBy('name')->get(),
            'repuestos' => Repuesto::orderBy('nombre')->get(),
        ]);
    }

    public function update(UpdateServicioRequest $request, Servicio $servicio): RedirectResponse
    {
        $servicio->update($request->validated());
        $servicio->recalcularCostoTotal();

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $this->authorize('delete', $servicio);

        $servicio->delete();

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }

    public function agregarRepuesto(Request $request, Servicio $servicio): RedirectResponse
    {
        $this->authorize('update', $servicio);

        $data = $request->validate([
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $repuesto = Repuesto::findOrFail((int) $data['repuesto_id']);

        if ($data['cantidad'] > $repuesto->cantidad) {
            return back()->withErrors(['cantidad' => 'No hay suficiente stock disponible.']);
        }

        $servicio->detalles()->create([
            'repuesto_id' => $repuesto->id,
            'cantidad' => $data['cantidad'],
            'precio_unitario' => $repuesto->precio,
        ]);

        return back()->with('success', 'Repuesto agregado al servicio.');
    }

    public function quitarRepuesto(Servicio $servicio, DetalleServicio $detalle): RedirectResponse
    {
        $this->authorize('update', $servicio);

        $detalle->delete();

        return back()->with('success', 'Repuesto removido del servicio.');
    }
}
