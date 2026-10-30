<?php

use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirige a un invitado al login al intentar acceder a usuarios', function () {
    $this->get('/usuarios')->assertRedirect('/login');
});

it('permite a un admin ver el listado de usuarios', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)
        ->get('/usuarios')
        ->assertOk();
});

it('prohibe a un recepcionista acceder a usuarios', function () {
    $recepcionista = User::factory()->create(['rol' => 'recepcionista']);

    $this->actingAs($recepcionista)
        ->get('/usuarios')
        ->assertForbidden();
});

it('prohibe a un mecanico acceder a usuarios', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico']);

    $this->actingAs($mecanico)
        ->get('/usuarios')
        ->assertForbidden();
});

it('permite a un admin crear un nuevo usuario', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)->post('/usuarios', [
        'name' => 'Usuario Prueba',
        'email' => 'prueba@test.com',
        'password' => '12345678',
        'password_confirmation' => '12345678',
        'rol' => 'mecanico',
        'puede_crear_servicios' => false,
    ])->assertRedirect('/usuarios');

    $this->assertDatabaseHas('users', [
        'email' => 'prueba@test.com',
        'rol' => 'mecanico',
    ]);
});

it('rechaza crear un usuario con email duplicado', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    User::factory()->create(['email' => 'repetido@test.com']);

    $this->actingAs($admin)->post('/usuarios', [
        'name' => 'Otro Usuario',
        'email' => 'repetido@test.com',
        'password' => '12345678',
        'password_confirmation' => '12345678',
        'rol' => 'mecanico',
    ])->assertSessionHasErrors('email');
});

it('permite a un admin actualizar un usuario sin cambiar la contraseña', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $usuario = User::factory()->create([
        'name' => 'Nombre Viejo',
        'rol' => 'mecanico',
    ]);
    $passwordOriginal = $usuario->password;

    $this->actingAs($admin)->put("/usuarios/{$usuario->id}", [
        'name' => 'Nombre Nuevo',
        'email' => $usuario->email,
        'rol' => 'recepcionista',
    ])->assertRedirect('/usuarios');

    $usuario->refresh();

    expect($usuario->name)->toBe('Nombre Nuevo');
    expect($usuario->rol)->toBe('recepcionista');
    expect($usuario->password)->toBe($passwordOriginal);
});

it('bloquea a un admin de eliminar su propia cuenta', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)
        ->delete("/usuarios/{$admin->id}")
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

it('elimina por completo a otro usuario de la base de datos', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $usuario = User::factory()->create(['rol' => 'mecanico']);

    $this->actingAs($admin)
        ->delete("/usuarios/{$usuario->id}")
        ->assertRedirect('/usuarios');

    $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
});

it('un usuario eliminado ya no puede iniciar sesion', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $usuario = User::factory()->create([
        'email' => 'eliminado@test.com',
        'rol' => 'mecanico',
        'password' => bcrypt('12345678'),
    ]);

    $this->actingAs($admin)->delete("/usuarios/{$usuario->id}");
    $this->post('/logout');

    $this->post('/login', [
        'email' => 'eliminado@test.com',
        'password' => '12345678',
    ]);

    $this->assertGuest();
});

it('al eliminar un mecanico sus servicios se conservan sin asignar', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $mecanico = User::factory()->create(['rol' => 'mecanico']);
    $servicio = Servicio::factory()->create(['mecanico_id' => $mecanico->id]);

    $this->actingAs($admin)
        ->delete("/usuarios/{$mecanico->id}")
        ->assertRedirect('/usuarios');

    $this->assertDatabaseMissing('users', ['id' => $mecanico->id]);
    expect($servicio->fresh()->mecanico_id)->toBeNull();
});

it('al eliminar el usuario de un cliente se conserva su ficha de cliente', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $cliente = Cliente::factory()->create();
    $usuarioCliente = User::factory()->create([
        'rol' => 'cliente',
        'cliente_id' => $cliente->id,
    ]);

    $this->actingAs($admin)
        ->delete("/usuarios/{$usuarioCliente->id}")
        ->assertRedirect('/usuarios');

    $this->assertDatabaseMissing('users', ['id' => $usuarioCliente->id]);
    $this->assertDatabaseHas('clientes', ['id' => $cliente->id]);
});
