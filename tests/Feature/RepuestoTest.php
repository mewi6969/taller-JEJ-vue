<?php

use App\Models\Repuesto;
use App\Models\User;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('redirige a los invitados al login', function () {
    $this->get('/repuestos')->assertRedirect('/login');
});

it('permite a un administrador ver el listado de repuestos', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)->get('/repuestos')->assertOk();
});

it('no permite a un mecanico ver el listado de repuestos', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico']);

    $this->actingAs($mecanico)->get('/repuestos')->assertForbidden();
});

it('permite crear un repuesto con datos validos', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)->post('/repuestos', [
        'nombre' => 'Pastillas de freno',
        'descripcion' => 'Juego delantero',
        'precio' => 45000,
        'cantidad' => 20,
        'cantidad_minima' => 5,
    ])->assertRedirect('/repuestos');

    $this->assertDatabaseHas('repuestos', ['nombre' => 'Pastillas de freno']);
});

it('no permite crear un repuesto con cantidad negativa', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)->post('/repuestos', [
        'nombre' => 'Filtro de aceite',
        'precio' => 15000,
        'cantidad' => -3,
        'cantidad_minima' => 5,
    ])->assertSessionHasErrors('cantidad');
});

it('detecta correctamente el bajo stock de un repuesto', function () {
    $bajoStock = Repuesto::factory()->create(['cantidad' => 2, 'cantidad_minima' => 5]);
    $stockOk = Repuesto::factory()->create(['cantidad' => 20, 'cantidad_minima' => 5]);

    expect($bajoStock->bajoStock())->toBeTrue();
    expect($stockOk->bajoStock())->toBeFalse();
});

it('elimina un repuesto de forma logica', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $repuesto = Repuesto::factory()->create();

    $this->actingAs($admin)
        ->delete("/repuestos/{$repuesto->id}")
        ->assertRedirect('/repuestos');

    $this->assertSoftDeleted('repuestos', ['id' => $repuesto->id]);
});
