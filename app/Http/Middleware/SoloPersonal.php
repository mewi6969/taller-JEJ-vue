<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloPersonal
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->esCliente()) {
            return redirect('/portal');
        }

        return $next($request);
    }
}
