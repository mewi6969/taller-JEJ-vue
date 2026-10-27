<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RestablecerContrasenaController extends Controller
{
    public function create(Request $request, string $token): Response
    {
        return Inertia::render('auth/RestablecerContrasena', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.required' => 'Escribe tu correo.',
            'email.email' => 'Escribe un correo válido.',
            'password.required' => 'Escribe la contraseña nueva.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // El 'rol' => 'cliente' es una segunda barrera: aunque alguien
        // tuviera un token de un usuario del personal, no serviría aquí.
        $estado = Password::reset(
            [...$datos, 'rol' => 'cliente'],
            function ($usuario, string $password) {
                $usuario->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($usuario));
            }
        );

        if ($estado === Password::PASSWORD_RESET) {
            return redirect('/login')->with('status', 'Contraseña cambiada. Ya puedes iniciar sesión.');
        }

        return back()->withErrors([
            'email' => 'El enlace no es válido o ya venció. Pide uno nuevo.',
        ]);
    }
}
