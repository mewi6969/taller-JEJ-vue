<?php

use App\Mail\ServicioTerminadoMail;
use App\Models\Cliente;
use App\Models\Motocicleta;
use App\Models\Servicio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('envia un correo cuando el servicio se marca como terminado y el cliente tiene email', function () {
    Mail::fake();

    $cliente = Cliente::factory()->create(['email' => 'cliente@test.com']);
    $motocicleta = Motocicleta::factory()->create(['cliente_id' => $cliente->id]);
    $servicio = Servicio::factory()->create([
        'motocicleta_id' => $motocicleta->id,
        'estado' => 'pendiente',
    ]);

    $servicio->update(['estado' => 'terminado']);

    Mail::assertSent(ServicioTerminadoMail::class, function ($mail) use ($servicio) {
        return $mail->servicio->id === $servicio->id;
    });
});

it('no envia correo si el cliente no tiene email registrado', function () {
    Mail::fake();

    $cliente = Cliente::factory()->create(['email' => null]);
    $motocicleta = Motocicleta::factory()->create(['cliente_id' => $cliente->id]);
    $servicio = Servicio::factory()->create([
        'motocicleta_id' => $motocicleta->id,
        'estado' => 'pendiente',
    ]);

    $servicio->update(['estado' => 'terminado']);

    Mail::assertNothingSent();
});

it('no envia correo si el servicio se actualiza pero el estado no cambia a terminado', function () {
    Mail::fake();

    $cliente = Cliente::factory()->create(['email' => 'cliente@test.com']);
    $motocicleta = Motocicleta::factory()->create(['cliente_id' => $cliente->id]);
    $servicio = Servicio::factory()->create([
        'motocicleta_id' => $motocicleta->id,
        'estado' => 'pendiente',
    ]);

    $servicio->update(['costo_mano_obra' => 150000]);

    Mail::assertNothingSent();
});
