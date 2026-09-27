<?php

use App\Models\Cliente;
use App\Models\Motocicleta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirige a los invitados al login', function () {
    $this->get('/motocicletas')->assertRedirect('/login');
});

it('permite a un administrador ver el listado de motocicletas', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)->get('/motocicletas')->assertOk();
});

it('no permite a un mecanico ver el listado de motocicletas', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico']);

    $this->actingAs($mecanico)->get('/motocicletas')->assertForbidden();
});

it('permite crear una motocicleta con datos validos', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $cliente = Cliente::factory()->create();

    $this->actingAs($admin)->post('/motocicletas', [
        'cliente_id' => $cliente->id,
        'placa' => 'ABC123',
        'marca' => 'Honda',
        'modelo' => 'Splendor',
        'anio' => 2022,
        'cilindraje' => 110,
        'color' => 'Rojo',
    ])->assertRedirect('/motocicletas');

    $this->assertDatabaseHas('motocicletas', ['placa' => 'ABC123']);
});

it('no permite crear una motocicleta con placa repetida', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $cliente = Cliente::factory()->create();
    Motocicleta::factory()->create(['placa' => 'ABC123']);

    $this->actingAs($admin)->post('/motocicletas', [
        'cliente_id' => $cliente->id,
        'placa' => 'ABC123',
        'marca' => 'Yamaha',
        'modelo' => 'FZ',
    ])->assertSessionHasErrors('placa');
});

it('elimina una motocicleta de forma logica', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $moto = Motocicleta::factory()->create();

    $this->actingAs($admin)
        ->delete("/motocicletas/{$moto->id}")
        ->assertRedirect('/motocicletas');

    $this->assertSoftDeleted('motocicletas', ['id' => $moto->id]);
});
