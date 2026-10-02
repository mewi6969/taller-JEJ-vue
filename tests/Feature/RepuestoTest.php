<?php

use App\Models\Repuesto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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

it('suma al stock existente en vez de duplicar cuando el nombre ya existe', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $existente = Repuesto::factory()->create([
        'nombre' => 'Filtro de aceite',
        'cantidad' => 10,
    ]);

    // Distinto uso de mayúsculas y espacios sobrantes: sigue siendo el mismo repuesto
    $this->actingAs($admin)->post('/repuestos', [
        'nombre' => '  FILTRO de ACEITE ',
        'descripcion' => 'Lo que sea',
        'precio' => 20000,
        'cantidad' => 5,
        'cantidad_minima' => 3,
    ])
        ->assertRedirect('/repuestos')
        ->assertSessionHas('success');

    expect(Repuesto::count())->toBe(1);
    expect($existente->fresh()->cantidad)->toBe(15);
});

it('crea un repuesto nuevo cuando el nombre no existe', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    Repuesto::factory()->create(['nombre' => 'Filtro de aceite']);

    $this->actingAs($admin)->post('/repuestos', [
        'nombre' => 'Espejo retrovisor',
        'descripcion' => 'Universal',
        'precio' => 25000,
        'cantidad' => 8,
        'cantidad_minima' => 2,
    ])->assertRedirect('/repuestos');

    expect(Repuesto::count())->toBe(2);
    $this->assertDatabaseHas('repuestos', [
        'nombre' => 'Espejo retrovisor',
        'cantidad' => 8,
    ]);
});

it('no cambia el precio ni la descripcion del repuesto original al sumar stock', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $existente = Repuesto::factory()->create([
        'nombre' => 'Bujia NGK',
        'descripcion' => 'Descripcion original',
        'precio' => 15000,
        'cantidad' => 10,
    ]);

    $this->actingAs($admin)->post('/repuestos', [
        'nombre' => 'Bujia NGK',
        'descripcion' => 'Descripcion distinta',
        'precio' => 99999,
        'cantidad' => 4,
        'cantidad_minima' => 3,
    ])->assertRedirect('/repuestos');

    $existente->refresh();

    expect($existente->cantidad)->toBe(14);
    expect($existente->descripcion)->toBe('Descripcion original');
    expect((float) $existente->precio)->toBe(15000.0);
});
