<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class OlvidoContrasenaController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('auth/OlvidoContrasena', [
            'status' => session('status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Escribe tu correo.',
            'email.email' => 'Escribe un correo válido.',
        ]);

        // Solo los clientes del portal pueden recuperar por correo.
        // El personal lo restablece un administrador desde /usuarios.
        Password::sendResetLink([
            'email' => $datos['email'],
            'rol' => 'cliente',
        ]);

        // Mismo mensaje exista o no el correo, sea cliente o personal,
        // para no revelar quién tiene cuenta.
        return back()->with(
            'status',
            'Si el correo está registrado, te enviamos un enlace para cambiar la contraseña.'
        );
    }
}
