<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Inertia\Inertia;
use Inertia\Response;

class ClienteController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Cliente::class);

        $clientes = Cliente::query()
            ->when(request('buscar'), function ($query, $buscar) {
                $query->where('nombre', 'ilike', "%{$buscar}%")
                    ->orWhere('apellido', 'ilike', "%{$buscar}%")
                    ->orWhere('documento', 'ilike', "%{$buscar}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('clientes/Index', [
            'clientes' => $clientes,
            'filtros' => request()->only('buscar'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Cliente::class);

        return Inertia::render('clientes/Create');
    }

    public function store(StoreClienteRequest $request)
    {
        Cliente::create($request->validated());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente): Response
    {
        $this->authorize('update', $cliente);

        return Inertia::render('clientes/Edit', [
            'cliente' => $cliente,
        ]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $this->authorize('delete', $cliente);

        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
