<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirige a los invitados al login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('permite a un administrador ver el dashboard completo', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('solo_mecanico', false)
        );
});

it('muestra al mecánico solo su resumen', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico']);

    $this->actingAs($mecanico)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('solo_mecanico', true)
            ->where('ingresos_mes', null)
        );
});
