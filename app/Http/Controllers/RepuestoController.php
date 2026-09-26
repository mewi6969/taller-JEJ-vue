<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepuestoRequest;
use App\Http\Requests\UpdateRepuestoRequest;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RepuestoController extends Controller
{
    public function index(): Response
{
    $this->authorize('viewAny', Repuesto::class);

    $repuestos = Repuesto::query()
        ->when(request('buscar'), function ($query, $buscar) {
            $query->where('nombre', 'like', "%{$buscar}%");
        })
        ->orderBy('nombre')
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('repuestos/Index', [
        'repuestos' => $repuestos,
        'filtros' => request()->only('buscar'),
    ]);
}

    public function create(): Response
{
    $this->authorize('create', Repuesto::class);

    return Inertia::render('repuestos/Create');
}

    public function store(StoreRepuestoRequest $request): RedirectResponse
    {
        Repuesto::create($request->validated());

        return redirect()->route('repuestos.index')
            ->with('success', 'Repuesto creado correctamente.');
    }

    public function edit(Repuesto $repuesto): Response
{
    $this->authorize('update', $repuesto);

    return Inertia::render('repuestos/Edit', [
        'repuesto' => $repuesto,
    ]);
}

    public function update(UpdateRepuestoRequest $request, Repuesto $repuesto): RedirectResponse
    {
        $repuesto->update($request->validated());

        return redirect()->route('repuestos.index')
            ->with('success', 'Repuesto actualizado correctamente.');
    }

    public function destroy(Repuesto $repuesto): RedirectResponse
    {
        $this->authorize('delete', $repuesto);

        $repuesto->delete();

        return redirect()->route('repuestos.index')
            ->with('success', 'Repuesto eliminado correctamente.');
    }
}
