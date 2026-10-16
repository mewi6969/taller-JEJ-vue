<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PortalController extends Controller
{
    public function index(Request $request): Response
    {
        $clienteId = $request->user()->cliente_id;

        $facturas = Factura::query()
            ->whereHas('servicio.motocicleta', fn ($q) => $q->where('cliente_id', $clienteId))
            ->with('servicio.motocicleta')
            ->latest()
            ->get();

        return Inertia::render('portal/Index', ['facturas' => $facturas]);
    }
}
