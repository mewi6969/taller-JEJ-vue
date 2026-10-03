<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacturaRequest;
use App\Http\Requests\UpdateFacturaRequest;
use App\Models\Factura;
use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class FacturaController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Factura::class);

        $facturas = Factura::with('servicio.motocicleta.cliente')
            ->orderByDesc('id')
            ->paginate(10);

        return Inertia::render('facturas/Index', [
            'facturas' => $facturas,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Factura::class);

        $servicios = Servicio::with('motocicleta.cliente')
            ->where('estado', 'terminado')
            ->whereDoesntHave('factura')
            ->get();

        return Inertia::render('facturas/Create', [
            'servicios' => $servicios,
        ]);
    }

    public function store(StoreFacturaRequest $request): RedirectResponse
    {
        $this->authorize('create', Factura::class);

        $servicio = Servicio::findOrFail($request->integer('servicio_id'));
        $descuento = $request->descuento ?? 0;

        Factura::create([
            'servicio_id' => $servicio->id,
            'subtotal' => $servicio->costo_total,
            'descuento' => $descuento,
            'total' => $servicio->costo_total - $descuento,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('facturas.index');
    }

    public function edit(Factura $factura): Response
    {
        $this->authorize('update', $factura);

        $factura->load('servicio.motocicleta.cliente');

        return Inertia::render('facturas/Edit', [
            'factura' => $factura,
        ]);
    }

    public function update(UpdateFacturaRequest $request, Factura $factura): RedirectResponse
    {
        $this->authorize('update', $factura);

        $datos = $request->validated();

        $esEfectivo = ($datos['estado'] ?? null) === 'pagada'
            && ($datos['metodo_pago'] ?? null) === 'efectivo';

        if ($esEfectivo) {
            $datos['cambio'] = round((float) $datos['monto_recibido'] - (float) $factura->total, 2);
        } else {
            $datos['monto_recibido'] = null;
            $datos['cambio'] = null;
        }

        $factura->update($datos);

        return redirect()->route('facturas.index');
    }

    public function destroy(Factura $factura): RedirectResponse
    {
        $this->authorize('delete', $factura);

        $factura->delete();

        return redirect()->route('facturas.index');
    }

    public function pdf(Factura $factura): HttpResponse
    {
        $this->authorize('view', $factura);

        $factura->load('servicio.motocicleta.cliente', 'servicio.detalles.repuesto');

        return Pdf::loadView('pdf.factura', ['factura' => $factura])
            ->download("factura-{$factura->numero_factura}.pdf");
    }
}
