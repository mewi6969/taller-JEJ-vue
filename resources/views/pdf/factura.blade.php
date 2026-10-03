<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #1f2430; }
        h1 { font-size: 20px; margin-bottom: 0; }
        .encabezado { width: 100%; margin-bottom: 10px; }
        .encabezado td { border: none; padding: 0; vertical-align: middle; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .totales { margin-top: 20px; width: 300px; margin-left: auto; }
        .totales td { border: none; padding: 4px 8px; }
        .pago { margin-top: 20px; width: 300px; margin-left: auto; }
        .pago td { border: none; padding: 4px 8px; }
        .pago .titulo { font-weight: bold; padding-bottom: 6px; border-bottom: 1px solid #ccc; }
    </style>
</head>
<body>
    <table class="encabezado">
        <tr>
            <td style="width: 80px;">
                <img src="{{ public_path('images/logo/logo-jej.png') }}" alt="Taller JEJ" width="70">
            </td>
            <td>
                <h1>Taller JEJ</h1>
            </td>
        </tr>
    </table>
    <p>Factura {{ $factura->numero_factura }}</p>
    <p>Fecha: {{ $factura->created_at->format('d/m/Y') }}</p>

    <p>
        <strong>Cliente:</strong>
        {{ $factura->servicio->motocicleta->cliente->nombre }}
        {{ $factura->servicio->motocicleta->cliente->apellido }}<br>
        <strong>Motocicleta:</strong> {{ $factura->servicio->motocicleta->placa }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Mano de obra</td>
                <td>${{ number_format($factura->servicio->costo_mano_obra, 2) }}</td>
            </tr>
            @foreach ($factura->servicio->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->repuesto?->nombre ?? 'Repuesto eliminado' }} (x{{ $detalle->cantidad }})</td>
                    <td>${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totales">
        <tr>
            <td>Subtotal</td>
            <td>${{ number_format($factura->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Descuento</td>
            <td>${{ number_format($factura->descuento, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>${{ number_format($factura->total, 2) }}</strong></td>
        </tr>
    </table>

    @if ($factura->estado === 'pagada')
        <table class="pago">
            <tr>
                <td colspan="2" class="titulo">Datos del pago</td>
            </tr>
            @if ($factura->metodo_pago)
                <tr>
                    <td>Método de pago</td>
                    <td>{{ ucfirst($factura->metodo_pago) }}</td>
                </tr>
            @endif
            @if ($factura->fecha_pago)
                <tr>
                    <td>Fecha de pago</td>
                    <td>{{ $factura->fecha_pago->format('d/m/Y') }}</td>
                </tr>
            @endif
            @if ($factura->metodo_pago === 'efectivo' && $factura->monto_recibido !== null)
                <tr>
                    <td>Monto recibido</td>
                    <td>${{ number_format($factura->monto_recibido, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Devuelta</strong></td>
                    <td><strong>${{ number_format($factura->cambio, 2) }}</strong></td>
                </tr>
            @endif
            @if ($factura->metodo_pago === 'tarjeta' && $factura->tarjeta_ultimos4)
                <tr>
                    <td>Tarjeta</td>
                    <td>**** {{ $factura->tarjeta_ultimos4 }}</td>
                </tr>
                <tr>
                    <td>N.º de aprobación</td>
                    <td>{{ $factura->tarjeta_aprobacion }}</td>
                </tr>
            @endif
        </table>
    @endif

    @if ($factura->estado === 'pendiente' && file_exists(public_path('images/pagos/qr-transferencia.png')))
        <table class="pago">
            <tr>
                <td class="titulo">Paga por transferencia</td>
            </tr>
            <tr>
                <td style="text-align: center; padding-top: 10px;">
                    <img src="{{ public_path('images/pagos/qr-transferencia.png') }}" alt="QR de pago" width="170">
                </td>
            </tr>
        </table>
    @endif
</body>
</html>
