<?php

namespace App\Http\Controllers;

use App\Mail\CodigoVerificacionMail;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegistroClienteController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('auth/Registro');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'documento' => ['required', 'string', 'max:30'],
            'telefono' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $cliente = Cliente::whereRaw('lower(email) = ?', [$datos['email']])->first();

        if ($cliente && User::where('cliente_id', $cliente->id)->exists()) {
            return back()->withErrors([
                'email' => 'Ya existe una cuenta para este cliente. Si no recuerdas la contraseña, comunícate con el taller.',
            ]);
        }

        if (! $cliente && Cliente::withTrashed()->where('documento', $datos['documento'])->exists()) {
            return back()->withErrors([
                'documento' => 'Este documento ya está registrado. Comunícate con el taller para actualizar tus datos.',
            ]);
        }

        $codigo = (string) random_int(100000, 999999);

        $user = DB::transaction(function () use ($cliente, $datos, $codigo) {
            $cliente ??= Cliente::create([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'documento' => $datos['documento'],
                'telefono' => $datos['telefono'],
                'email' => $datos['email'],
            ]);

            $user = new User;
            $user->forceFill([
                'name' => trim($datos['nombre'].' '.$datos['apellido']),
                'email' => $datos['email'],
                'password' => Hash::make($datos['password']),
                'rol' => 'cliente',
                'cliente_id' => $cliente->id,
                'codigo_verificacion' => Hash::make($codigo),
                'codigo_expira_en' => now()->addMinutes(15),
            ])->save();

            return $user;
        });

        Mail::to($user->email)->send(new CodigoVerificacionMail($codigo));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/verificar-correo');
    }
}
