<?php

use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirige a los invitados al login', function () {
    $this->get('/calendario')->assertRedirect('/login');
});

it('muestra el calendario con los servicios a un administrador', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    Servicio::factory()->count(2)->create([
        'fecha_ingreso' => now()->toDateString(),
    ]);

    $this->actingAs($admin)
        ->get('/calendario')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Calendario')
            ->has('servicios', 2)
        );
});

it('muestra a un mecanico solo los servicios que tiene asignados', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico']);
    $otro = User::factory()->create(['rol' => 'mecanico']);

    Servicio::factory()->create([
        'mecanico_id' => $mecanico->id,
        'fecha_ingreso' => now()->toDateString(),
    ]);
    Servicio::factory()->count(2)->create([
        'mecanico_id' => $otro->id,
        'fecha_ingreso' => now()->toDateString(),
    ]);

    $this->actingAs($mecanico)
        ->get('/calendario')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Calendario')
            ->has('servicios', 1)
        );
});
