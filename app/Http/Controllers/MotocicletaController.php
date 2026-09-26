<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMotocicletaRequest;
use App\Http\Requests\UpdateMotocicletaRequest;
use App\Models\Cliente;
use App\Models\Motocicleta;
use Inertia\Inertia;
use Inertia\Response;

class MotocicletaController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Motocicleta::class);

        $motocicletas = Motocicleta::query()
            ->with('cliente')
            ->when(request('buscar'), function ($query, $buscar) {
                $query->where('placa', 'ilike', "%{$buscar}%")
                    ->orWhere('marca', 'ilike', "%{$buscar}%")
                    ->orWhere('modelo', 'ilike', "%{$buscar}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('motocicletas/Index', [
            'motocicletas' => $motocicletas,
            'filtros' => request()->only('buscar'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Motocicleta::class);

        return Inertia::render('motocicletas/Create', [
            'clientes' => Cliente::orderBy('nombre')->get(['id', 'nombre', 'apellido', 'documento']),
        ]);
    }

    public function store(StoreMotocicletaRequest $request)
    {
        Motocicleta::create($request->validated());

        return redirect()->route('motocicletas.index')
            ->with('success', 'Motocicleta registrada correctamente.');
    }

    public function edit(Motocicleta $motocicleta): Response
    {
        $this->authorize('update', $motocicleta);

        return Inertia::render('motocicletas/Edit', [
            'motocicleta' => $motocicleta,
            'clientes' => Cliente::orderBy('nombre')->get(['id', 'nombre', 'apellido', 'documento']),
        ]);
    }

    public function update(UpdateMotocicletaRequest $request, Motocicleta $motocicleta)
    {
        $motocicleta->update($request->validated());

        return redirect()->route('motocicletas.index')
            ->with('success', 'Motocicleta actualizada correctamente.');
    }

    public function destroy(Motocicleta $motocicleta)
    {
        $this->authorize('delete', $motocicleta);

        $motocicleta->delete();

        return redirect()->route('motocicletas.index')
            ->with('success', 'Motocicleta eliminada correctamente.');
    }
}
