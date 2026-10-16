<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloCliente
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->esCliente()) {
            return redirect('/dashboard');
        }

        if ($user->email_verified_at === null) {
            return redirect('/verificar-correo');
        }

        return $next($request);
    }
}
