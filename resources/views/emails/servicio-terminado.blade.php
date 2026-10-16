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

    <p>Hola,</p>
    <p>Tu código de verificación para el portal de Taller JEJ es:</p>
    <h2 style="letter-spacing: 4px;">{{ $codigo }}</h2>
    <p>Vence en 15 minutos. Si no fuiste tú, ignora este mensaje.</p>

</body>
</html>
