<?php

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('redirige a los invitados al login', function () {
    $this->get('/clientes')->assertRedirect('/login');
});

it('permite a un administrador ver el listado de clientes', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)
        ->get('/clientes')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('clientes/Index'));
});

it('no permite a un mecanico ver el listado de clientes', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico']);

    $this->actingAs($mecanico)
        ->get('/clientes')
        ->assertForbidden();
});

it('permite crear un cliente con datos validos', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)->post('/clientes', [
        'nombre' => 'Erwin',
        'apellido' => 'Grizales',
        'documento' => '1112879490',
        'telefono' => '3206181331',
        'email' => 'erwin@example.com',
        'direccion' => 'Corinto',
    ])->assertRedirect('/clientes');

    $this->assertDatabaseHas('clientes', ['documento' => '1112879490']);
});

it('no permite crear un cliente con documento repetido', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    Cliente::factory()->create(['documento' => '1112879490']);

    $this->actingAs($admin)->post('/clientes', [
        'nombre' => 'Otro',
        'apellido' => 'Cliente',
        'documento' => '1112879490',
        'telefono' => '3000000000',
    ])->assertSessionHasErrors('documento');
});

it('permite actualizar un cliente existente', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $cliente = Cliente::factory()->create();

    $this->actingAs($admin)->put("/clientes/{$cliente->id}", [
        'nombre' => 'Actualizado',
        'apellido' => $cliente->apellido,
        'documento' => $cliente->documento,
        'telefono' => $cliente->telefono,
        'email' => $cliente->email,
        'direccion' => $cliente->direccion,
    ])->assertRedirect('/clientes');

    expect($cliente->fresh()->nombre)->toBe('Actualizado');
});

it('elimina un cliente de forma logica (soft delete)', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $cliente = Cliente::factory()->create();

    $this->actingAs($admin)
        ->delete("/clientes/{$cliente->id}")
        ->assertRedirect('/clientes');

    $this->assertSoftDeleted('clientes', ['id' => $cliente->id]);
});
