<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

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

    public function pdf(Request $request, Factura $factura): HttpResponse
    {
        $factura->load('servicio.motocicleta.cliente', 'servicio.detalles.repuesto');

        $clienteId = $request->user()->cliente_id;

        abort_unless(
            $clienteId !== null
                && $factura->servicio?->motocicleta?->cliente_id === $clienteId,
            403,
        );

        return Pdf::loadView('pdf.factura', ['factura' => $factura])
            ->download("factura-{$factura->numero_factura}.pdf");
    }
}
