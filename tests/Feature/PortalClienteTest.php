<?php

use App\Mail\CodigoVerificacionMail;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Motocicleta;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

function usuarioCliente(array $extra = []): User
{
    return User::factory()->create([
        'rol' => 'cliente',
        'cliente_id' => Cliente::factory()->create()->id,
        ...$extra,
    ]);
}

it('registra un cliente nuevo y envía el código', function () {
    Mail::fake();

    $this->post('/registro', [
        'nombre' => 'Ana',
        'apellido' => 'Pérez',
        'documento' => '1061000001',
        'telefono' => '3001234567',
        'email' => 'ana@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/verificar-correo');

    $user = User::where('email', 'ana@example.com')->first();

    expect($user->rol)->toBe('cliente')
        ->and($user->cliente_id)->not->toBeNull()
        ->and($user->email_verified_at)->toBeNull();

    Mail::assertSent(CodigoVerificacionMail::class);
});

it('enlaza con el cliente existente que tiene el mismo correo', function () {
    Mail::fake();
    $cliente = Cliente::factory()->create(['email' => 'luis@example.com']);

    $this->post('/registro', [
        'nombre' => 'Luis',
        'apellido' => 'Gómez',
        'documento' => '1061000002',
        'telefono' => '3007654321',
        'email' => 'Luis@Example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect(Cliente::count())->toBe(1)
        ->and(User::where('email', 'luis@example.com')->first()->cliente_id)->toBe($cliente->id);
});

it('verifica el correo con el código correcto', function () {
    $user = usuarioCliente([
        'email_verified_at' => null,
        'codigo_verificacion' => Hash::make('123456'),
        'codigo_expira_en' => now()->addMinutes(10),
    ]);

    $this->actingAs($user)
        ->post('/verificar-correo', ['codigo' => '123456'])
        ->assertRedirect('/portal');

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

it('rechaza un código incorrecto', function () {
    $user = usuarioCliente([
        'email_verified_at' => null,
        'codigo_verificacion' => Hash::make('123456'),
        'codigo_expira_en' => now()->addMinutes(10),
    ]);

    $this->actingAs($user)
        ->post('/verificar-correo', ['codigo' => '000000'])
        ->assertSessionHasErrors('codigo');
});

it('manda al cliente sin verificar a verificar su correo', function () {
    $user = usuarioCliente(['email_verified_at' => null]);

    $this->actingAs($user)->get('/portal')->assertRedirect('/verificar-correo');
});

it('no deja al cliente entrar a las rutas del personal', function () {
    $this->actingAs(usuarioCliente())->get('/dashboard')->assertRedirect('/portal');
    $this->actingAs(usuarioCliente())->get('/clientes')->assertRedirect('/portal');
});

it('el cliente solo ve sus propias facturas', function () {
    $mio = Cliente::factory()->create();
    $otro = Cliente::factory()->create();

    $facturaDe = fn (Cliente $c) => Factura::factory()->create([
        'servicio_id' => Servicio::factory()->create([
            'motocicleta_id' => Motocicleta::factory()->create(['cliente_id' => $c->id])->id,
        ])->id,
    ]);

    $propia = $facturaDe($mio);
    $facturaDe($otro);

    $user = User::factory()->create(['rol' => 'cliente', 'cliente_id' => $mio->id]);

    $this->actingAs($user)->get('/portal')->assertInertia(
        fn ($page) => $page->component('portal/Index')
            ->has('facturas', 1)
            ->where('facturas.0.id', $propia->id)
    );
});

it('permite al cliente descargar el pdf de su propia factura', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->create([
        'rol' => 'cliente',
        'cliente_id' => $cliente->id,
        'email_verified_at' => now(),
    ]);
    $moto = Motocicleta::factory()->create(['cliente_id' => $cliente->id]);
    $servicio = Servicio::factory()->create(['motocicleta_id' => $moto->id, 'estado' => 'terminado']);
    $factura = Factura::factory()->create(['servicio_id' => $servicio->id]);

    $this->actingAs($usuario)
        ->get("/portal/facturas/{$factura->id}/pdf")
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('prohibe al cliente descargar el pdf de una factura ajena', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->create([
        'rol' => 'cliente',
        'cliente_id' => $cliente->id,
        'email_verified_at' => now(),
    ]);
    $otraMoto = Motocicleta::factory()->create();
    $servicio = Servicio::factory()->create(['motocicleta_id' => $otraMoto->id, 'estado' => 'terminado']);
    $ajena = Factura::factory()->create(['servicio_id' => $servicio->id]);

    $this->actingAs($usuario)
        ->get("/portal/facturas/{$ajena->id}/pdf")
        ->assertForbidden();
});

it('rechaza un correo sin punto ni terminación', function () {
    Mail::fake();

    $this->post('/registro', [
        'nombre' => 'Prueba',
        'apellido' => 'Uno',
        'documento' => '1061000003',
        'telefono' => '3001112233',
        'email' => 'prueba@gmail',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');

    expect(User::where('email', 'prueba@gmail')->exists())->toBeFalse();
    Mail::assertNothingSent();
});

it('rechaza registrar un correo que ya tiene una cuenta activa', function () {
    Mail::fake();
    usuarioCliente(['email' => 'activo@example.com']);

    $this->post('/registro', [
        'nombre' => 'Otro',
        'apellido' => 'Usuario',
        'documento' => '1061000004',
        'telefono' => '3004445566',
        'email' => 'activo@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');
});

it('permite volver a registrarse con el mismo documento y correo tras borrar al cliente', function () {
    Mail::fake();

    $cliente = Cliente::factory()->create([
        'documento' => '1061000005',
        'email' => 'vuelve@example.com',
    ]);
    $user = User::factory()->create([
        'rol' => 'cliente',
        'cliente_id' => $cliente->id,
        'email' => 'vuelve@example.com',
    ]);

    // Borrado como lo hace el panel: archiva la cuenta y el cliente
    $admin = User::factory()->create(['rol' => 'admin']);
    $this->actingAs($admin)->delete("/clientes/{$cliente->id}");

    expect(Cliente::withTrashed()->find($cliente->id)->trashed())->toBeTrue()
        ->and(User::withTrashed()->find($user->id)->trashed())->toBeTrue();

    auth()->logout();

    $this->post('/registro', [
        'nombre' => 'Vuelve',
        'apellido' => 'Cliente',
        'documento' => '1061000005',
        'telefono' => '3009998877',
        'email' => 'vuelve@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/verificar-correo');

    $nuevo = User::where('email', 'vuelve@example.com')->first();

    expect($nuevo)->not->toBeNull()
        ->and($nuevo->id)->not->toBe($user->id)
        ->and($nuevo->cliente_id)->not->toBe($cliente->id);
});
