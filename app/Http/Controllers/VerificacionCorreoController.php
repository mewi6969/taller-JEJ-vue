<?php

namespace App\Http\Controllers;

use App\Mail\CodigoVerificacionMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class VerificacionCorreoController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user->esCliente()) {
            return redirect('/dashboard');
        }

        if ($user->email_verified_at !== null) {
            return redirect('/portal');
        }

        return Inertia::render('auth/VerificarCorreo', ['email' => $user->email]);
    }

    public function verificar(Request $request): RedirectResponse
    {
        $request->validate(['codigo' => ['required', 'digits:6']]);

        $user = $request->user();

        if (
            $user->codigo_verificacion === null
            || $user->codigo_expira_en === null
            || now()->greaterThan($user->codigo_expira_en)
            || ! Hash::check((string) $request->input('codigo'), $user->codigo_verificacion)
        ) {
            return back()->withErrors(['codigo' => 'El código es incorrecto o ya venció.']);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'codigo_verificacion' => null,
            'codigo_expira_en' => null,
        ])->save();

        return redirect('/portal')->with('success', '¡Correo verificado! Bienvenido al portal.');
    }

    public function reenviar(Request $request): RedirectResponse
    {
        $user = $request->user();
        $codigo = (string) random_int(100000, 999999);

        $user->forceFill([
            'codigo_verificacion' => Hash::make($codigo),
            'codigo_expira_en' => now()->addMinutes(15),
        ])->save();

        Mail::to($user->email)->send(new CodigoVerificacionMail($codigo));

        return back()->with('success', 'Te enviamos un nuevo código.');
    }
}
