<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #1f2430; }
        h1 { font-size: 20px; margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .totales { margin-top: 20px; width: 300px; margin-left: auto; }
        .totales td { border: none; padding: 4px 8px; }
    </style>
</head>
<body>
    <h1>Taller JEJ</h1>
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
</body>
</html>
