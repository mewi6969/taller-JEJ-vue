<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        $usuarios = User::query()
            ->when(request('buscar'), function ($query, $buscar) {
                $query->where('name', 'like', "%{$buscar}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('usuarios/Index', [
            'usuarios' => $usuarios,
            'filtros' => request()->only('buscar'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('usuarios/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): Response
    {
        $this->authorize('update', $usuario);

        return Inertia::render('usuarios/Edit', [
            'usuario' => $usuario,
        ]);
    }

    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        $this->authorize('delete', $usuario);

        // Borrado definitivo: se va el usuario, sus sesiones abiertas y sus
        // solicitudes de recuperación. Los servicios que atendió quedan
        // "sin asignar" y, si era un cliente del portal, el registro del
        // cliente y su historial se conservan.
        DB::transaction(function () use ($usuario) {
            DB::table('sessions')->where('user_id', $usuario->id)->delete();
            DB::table('password_reset_tokens')->where('email', $usuario->email)->delete();

            $usuario->forceDelete();
        });

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado definitivamente.');
    }
}
