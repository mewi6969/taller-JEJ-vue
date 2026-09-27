<?php

use App\Models\Motocicleta;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirige a los invitados al login', function () {
    $this->get('/servicios')->assertRedirect('/login');
});

it('permite a un administrador ver el listado de servicios', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)->get('/servicios')->assertOk();
});

it('un mecanico solo ve los servicios que tiene asignados', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico']);
    $otroMecanico = User::factory()->create(['rol' => 'mecanico']);

    $moto = Motocicleta::factory()->create();
    $miServicio = Servicio::factory()->create(['motocicleta_id' => $moto->id, 'mecanico_id' => $mecanico->id]);
    Servicio::factory()->create(['motocicleta_id' => $moto->id, 'mecanico_id' => $otroMecanico->id]);

    $response = $this->actingAs($mecanico)->get('/servicios');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('servicios/Index')
        ->has('servicios.data', 1)
        ->where('servicios.data.0.id', $miServicio->id)
    );
});

it('permite a un administrador crear un servicio', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $moto = Motocicleta::factory()->create();

    $this->actingAs($admin)->post('/servicios', [
        'motocicleta_id' => $moto->id,
        'descripcion_problema' => 'No enciende',
        'costo_mano_obra' => 50000,
    ])->assertRedirect('/servicios');

    $this->assertDatabaseHas('servicios', [
        'motocicleta_id' => $moto->id,
        'estado' => 'pendiente',
    ]);
});

it('no permite a un mecanico sin permiso crear un servicio', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico', 'puede_crear_servicios' => false]);
    $moto = Motocicleta::factory()->create();

    $this->actingAs($mecanico)->post('/servicios', [
        'motocicleta_id' => $moto->id,
        'descripcion_problema' => 'No enciende',
        'costo_mano_obra' => 50000,
    ])->assertForbidden();
});

it('permite a un mecanico con permiso especial crear un servicio', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico', 'puede_crear_servicios' => true]);
    $moto = Motocicleta::factory()->create();

    $this->actingAs($mecanico)->post('/servicios', [
        'motocicleta_id' => $moto->id,
        'descripcion_problema' => 'No enciende',
        'costo_mano_obra' => 50000,
    ])->assertRedirect('/servicios');
});

it('descuenta el stock del repuesto al agregarlo a un servicio', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $servicio = Servicio::factory()->create();
    $repuesto = Repuesto::factory()->create(['cantidad' => 10, 'precio' => 20000]);

    $this->actingAs($admin)->post("/servicios/{$servicio->id}/repuestos", [
        'repuesto_id' => $repuesto->id,
        'cantidad' => 3,
    ])->assertRedirect();

    expect($repuesto->fresh()->cantidad)->toBe(7);
    expect(number_format($servicio->fresh()->costo_total, 2))
        ->toBe(number_format($servicio->costo_mano_obra + (3 * 20000), 2));
});

it('no permite agregar un repuesto si no hay stock suficiente', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $servicio = Servicio::factory()->create();
    $repuesto = Repuesto::factory()->create(['cantidad' => 2]);

    $this->actingAs($admin)->post("/servicios/{$servicio->id}/repuestos", [
        'repuesto_id' => $repuesto->id,
        'cantidad' => 5,
    ])->assertSessionHasErrors('cantidad');

    expect($repuesto->fresh()->cantidad)->toBe(2);
});

it('devuelve el stock al quitar un repuesto de un servicio', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $servicio = Servicio::factory()->create();
    $repuesto = Repuesto::factory()->create(['cantidad' => 10, 'precio' => 20000]);

    $detalle = $servicio->detalles()->create([
        'repuesto_id' => $repuesto->id,
        'cantidad' => 3,
        'precio_unitario' => 20000,
    ]);

    expect($repuesto->fresh()->cantidad)->toBe(7);

    $this->actingAs($admin)
        ->delete("/servicios/{$servicio->id}/repuestos/{$detalle->id}")
        ->assertRedirect();

    expect($repuesto->fresh()->cantidad)->toBe(10);
    expect($servicio->fresh()->costo_total)->toEqual($servicio->costo_mano_obra);
});

it('elimina un servicio de forma logica', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $servicio = Servicio::factory()->create();

    $this->actingAs($admin)
        ->delete("/servicios/{$servicio->id}")
        ->assertRedirect('/servicios');

    $this->assertSoftDeleted('servicios', ['id' => $servicio->id]);
});
