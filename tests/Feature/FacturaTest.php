<?php

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Motocicleta;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function crearServicioTerminado(float $costoTotal = 100000): Servicio
{
    $cliente = Cliente::factory()->create();
    $motocicleta = Motocicleta::factory()->create(['cliente_id' => $cliente->id]);

    return Servicio::factory()->create([
        'motocicleta_id' => $motocicleta->id,
        'estado' => 'terminado',
        'costo_total' => $costoTotal,
    ]);
}

it('redirige a un invitado al login al intentar acceder a facturas', function () {
    $this->get('/facturas')->assertRedirect('/login');
});

it('permite a un admin ver el listado de facturas', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)->get('/facturas')->assertOk();
});

it('permite a un recepcionista ver el listado de facturas', function () {
    $recepcionista = User::factory()->create(['rol' => 'recepcionista']);

    $this->actingAs($recepcionista)->get('/facturas')->assertOk();
});

it('prohibe a un mecanico acceder a facturas', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico']);

    $this->actingAs($mecanico)->get('/facturas')->assertForbidden();
});

it('permite crear una factura a partir de un servicio terminado', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $servicio = crearServicioTerminado(100000);

    $this->actingAs($admin)->post('/facturas', [
        'servicio_id' => $servicio->id,
        'descuento' => 10000,
    ])->assertRedirect('/facturas');

    $this->assertDatabaseHas('facturas', [
        'servicio_id' => $servicio->id,
        'subtotal' => 100000,
        'descuento' => 10000,
        'total' => 90000,
        'estado' => 'pendiente',
    ]);
});

it('no permite facturar un servicio que no esta terminado', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $cliente = Cliente::factory()->create();
    $motocicleta = Motocicleta::factory()->create(['cliente_id' => $cliente->id]);
    $servicio = Servicio::factory()->create([
        'motocicleta_id' => $motocicleta->id,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($admin)->post('/facturas', [
        'servicio_id' => $servicio->id,
    ])->assertSessionHasErrors('servicio_id');
});

it('no permite facturar el mismo servicio dos veces', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $servicio = crearServicioTerminado();

    Factura::factory()->create(['servicio_id' => $servicio->id]);

    $this->actingAs($admin)->post('/facturas', [
        'servicio_id' => $servicio->id,
    ])->assertSessionHasErrors('servicio_id');
});

it('permite a un admin marcar una factura como pagada', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $servicio = crearServicioTerminado();
    $factura = Factura::factory()->create(['servicio_id' => $servicio->id]);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'efectivo',
        'fecha_pago' => now()->toDateString(),
    ])->assertRedirect('/facturas');

    $this->assertDatabaseHas('facturas', [
        'id' => $factura->id,
        'estado' => 'pagada',
        'metodo_pago' => 'efectivo',
    ]);
});

it('prohibe a un recepcionista eliminar una factura', function () {
    $recepcionista = User::factory()->create(['rol' => 'recepcionista']);
    $servicio = crearServicioTerminado();
    $factura = Factura::factory()->create(['servicio_id' => $servicio->id]);

    $this->actingAs($recepcionista)
        ->delete("/facturas/{$factura->id}")
        ->assertForbidden();
});

it('permite a un admin eliminar (soft delete) una factura', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $servicio = crearServicioTerminado();
    $factura = Factura::factory()->create(['servicio_id' => $servicio->id]);

    $this->actingAs($admin)
        ->delete("/facturas/{$factura->id}")
        ->assertRedirect('/facturas');

    $this->assertSoftDeleted('facturas', ['id' => $factura->id]);
});
