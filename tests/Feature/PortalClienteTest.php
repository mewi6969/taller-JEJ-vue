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
