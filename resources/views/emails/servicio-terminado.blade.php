<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Servicio terminado</title>
</head>
<body style="font-family: sans-serif; color: #1f2430; line-height: 1.6;">
    <h2>¡Tu motocicleta ya está lista!</h2>

    <p>Hola {{ $servicio->motocicleta->cliente->nombre }},</p>

    <p>
        Te informamos que el servicio realizado a tu motocicleta con placa
        <strong>{{ $servicio->motocicleta->placa }}</strong> ha sido completado.
    </p>

    <p><strong>Descripción del problema:</strong> {{ $servicio->descripcion_problema }}</p>
    <p><strong>Costo total:</strong> ${{ number_format($servicio->costo_total, 2) }}</p>

    <p>Puedes pasar a recoger tu moto en nuestro horario habitual.</p>

    <p>Gracias por confiar en Taller JEJ.</p>
</body>
</html>
