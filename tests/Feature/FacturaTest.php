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

function crearFacturaPendiente(float $total = 100000): Factura
{
    $servicio = crearServicioTerminado($total);

    return Factura::factory()->create([
        'servicio_id' => $servicio->id,
        'subtotal' => $total,
        'descuento' => 0,
        'total' => $total,
        'estado' => 'pendiente',
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

it('permite a un admin marcar una factura como pagada en efectivo y guarda la devuelta', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'efectivo',
        'monto_recibido' => 120000,
        'fecha_pago' => now()->toDateString(),
    ])->assertRedirect('/facturas');

    $this->assertDatabaseHas('facturas', [
        'id' => $factura->id,
        'estado' => 'pagada',
        'metodo_pago' => 'efectivo',
        'monto_recibido' => 120000,
        'cambio' => 20000,
    ]);
});

it('guarda devuelta cero cuando el cliente paga el valor exacto en efectivo', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'efectivo',
        'monto_recibido' => 100000,
        'fecha_pago' => now()->toDateString(),
    ])->assertRedirect('/facturas');

    $this->assertDatabaseHas('facturas', [
        'id' => $factura->id,
        'monto_recibido' => 100000,
        'cambio' => 0,
    ]);
});

it('rechaza un pago en efectivo si el monto recibido no alcanza para el total', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'efectivo',
        'monto_recibido' => 80000,
        'fecha_pago' => now()->toDateString(),
    ])->assertSessionHasErrors('monto_recibido');

    $this->assertDatabaseHas('facturas', [
        'id' => $factura->id,
        'estado' => 'pendiente',
    ]);
});

it('exige el monto recibido cuando el pago es en efectivo', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'efectivo',
        'fecha_pago' => now()->toDateString(),
    ])->assertSessionHasErrors('monto_recibido');
});

it('no guarda monto ni devuelta cuando el pago es por transferencia', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'transferencia',
        'monto_recibido' => 500000,
        'fecha_pago' => now()->toDateString(),
    ])->assertRedirect('/facturas');

    $this->assertDatabaseHas('facturas', [
        'id' => $factura->id,
        'metodo_pago' => 'transferencia',
        'monto_recibido' => null,
        'cambio' => null,
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

it('guarda los ultimos 4 digitos y la aprobacion cuando se paga con tarjeta', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'tarjeta',
        'tarjeta_ultimos4' => '4242',
        'tarjeta_aprobacion' => 'AB-123456',
        'fecha_pago' => now()->toDateString(),
    ])->assertRedirect('/facturas');

    $this->assertDatabaseHas('facturas', [
        'id' => $factura->id,
        'estado' => 'pagada',
        'metodo_pago' => 'tarjeta',
        'tarjeta_ultimos4' => '4242',
        'tarjeta_aprobacion' => 'AB-123456',
        'monto_recibido' => null,
        'cambio' => null,
    ]);
});

it('exige los datos del voucher cuando el pago es con tarjeta', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'tarjeta',
        'fecha_pago' => now()->toDateString(),
    ])->assertSessionHasErrors(['tarjeta_ultimos4', 'tarjeta_aprobacion']);

    $this->assertDatabaseHas('facturas', [
        'id' => $factura->id,
        'estado' => 'pendiente',
    ]);
});

it('rechaza un numero de tarjeta completo en vez de los ultimos 4 digitos', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'tarjeta',
        'tarjeta_ultimos4' => '4242424242424242',
        'tarjeta_aprobacion' => 'AB-123456',
        'fecha_pago' => now()->toDateString(),
    ])->assertSessionHasErrors('tarjeta_ultimos4');

    $this->assertDatabaseMissing('facturas', [
        'tarjeta_ultimos4' => '4242424242424242',
    ]);
});

it('no guarda datos de tarjeta cuando el pago es en efectivo', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'efectivo',
        'monto_recibido' => 100000,
        'tarjeta_ultimos4' => '4242',
        'tarjeta_aprobacion' => 'AB-123456',
        'fecha_pago' => now()->toDateString(),
    ])->assertRedirect('/facturas');

    $this->assertDatabaseHas('facturas', [
        'id' => $factura->id,
        'metodo_pago' => 'efectivo',
        'tarjeta_ultimos4' => null,
        'tarjeta_aprobacion' => null,
    ]);
});

it('rechaza una fecha de pago futura', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $factura = crearFacturaPendiente(100000);

    $this->actingAs($admin)->put("/facturas/{$factura->id}", [
        'estado' => 'pagada',
        'metodo_pago' => 'transferencia',
        'fecha_pago' => now()->addDays(5)->toDateString(),
    ])->assertSessionHasErrors('fecha_pago');

    $this->assertDatabaseHas('facturas', [
        'id' => $factura->id,
        'estado' => 'pendiente',
    ]);
});
