<?php

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Repuesto;
use App\Models\User;

function clienteConPortal(): User
{
    return User::factory()->create([
        'rol' => 'cliente',
        'cliente_id' => Cliente::factory()->create()->id,
    ]);
}

function pedidoDe(User $user, array $items = []): Pedido
{
    $pedido = Pedido::create([
        'cliente_id' => $user->cliente_id,
        'estado' => 'pendiente',
    ]);

    foreach ($items as $repuesto) {
        $pedido->detalles()->create([
            'repuesto_id' => $repuesto->id,
            'cantidad' => 1,
            'precio_unitario' => $repuesto->precio,
        ]);
    }

    return $pedido;
}

it('muestra al cliente solo los repuestos con stock', function () {
    Repuesto::factory()->create(['cantidad' => 5]);
    Repuesto::factory()->create(['cantidad' => 0]);

    $this->actingAs(clienteConPortal())->get('/portal/catalogo')->assertInertia(
        fn ($page) => $page->component('portal/Catalogo')->has('repuestos', 1)
    );
});

it('crea un pedido con sus lineas y no descuenta el stock', function () {
    $repuesto = Repuesto::factory()->create(['cantidad' => 10, 'precio' => 20000]);
    $user = clienteConPortal();

    $this->actingAs($user)->post('/portal/pedidos', [
        'items' => [['repuesto_id' => $repuesto->id, 'cantidad' => 3]],
        'nota' => 'Para mi moto',
    ])->assertRedirect('/portal/pedidos');

    $pedido = Pedido::with('detalles')->first();

    expect($pedido->cliente_id)->toBe($user->cliente_id)
        ->and($pedido->estado)->toBe('pendiente')
        ->and($pedido->detalles)->toHaveCount(1)
        ->and($pedido->detalles->first()->cantidad)->toBe(3)
        ->and((float) $pedido->detalles->first()->precio_unitario)->toBe(20000.0)
        ->and($repuesto->refresh()->cantidad)->toBe(10);
});

it('rechaza un pedido con mas unidades que el stock', function () {
    $repuesto = Repuesto::factory()->create(['cantidad' => 2]);

    $this->actingAs(clienteConPortal())->post('/portal/pedidos', [
        'items' => [['repuesto_id' => $repuesto->id, 'cantidad' => 5]],
    ])->assertSessionHasErrors('items');

    expect(Pedido::count())->toBe(0);
});

it('rechaza un pedido vacio', function () {
    $this->actingAs(clienteConPortal())
        ->post('/portal/pedidos', ['items' => []])
        ->assertSessionHasErrors('items');
});

it('rechaza un repuesto repetido en el mismo pedido', function () {
    $repuesto = Repuesto::factory()->create(['cantidad' => 10]);

    $this->actingAs(clienteConPortal())->post('/portal/pedidos', [
        'items' => [
            ['repuesto_id' => $repuesto->id, 'cantidad' => 1],
            ['repuesto_id' => $repuesto->id, 'cantidad' => 2],
        ],
    ])->assertSessionHasErrors('items.1.repuesto_id');
});

it('el cliente solo ve sus propios pedidos', function () {
    $repuesto = Repuesto::factory()->create(['cantidad' => 10]);
    $yo = clienteConPortal();
    $otro = clienteConPortal();

    $mio = pedidoDe($yo, [$repuesto]);
    pedidoDe($otro, [$repuesto]);

    $this->actingAs($yo)->get('/portal/pedidos')->assertInertia(
        fn ($page) => $page->component('portal/Pedidos')
            ->has('pedidos', 1)
            ->where('pedidos.0.id', $mio->id)
    );
});

it('el personal no puede entrar al catalogo del cliente', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)->get('/portal/catalogo')->assertRedirect('/dashboard');
});

it('un invitado es enviado al login al pedir', function () {
    $this->post('/portal/pedidos', [])->assertRedirect('/login');
});

it('un cliente no verificado es enviado a verificar su correo', function () {
    $user = User::factory()->create([
        'rol' => 'cliente',
        'cliente_id' => Cliente::factory()->create()->id,
        'email_verified_at' => null,
    ]);

    $this->actingAs($user)->get('/portal/catalogo')->assertRedirect('/verificar-correo');
});

it('permite a un admin ver el listado de pedidos', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    pedidoDe(clienteConPortal(), [Repuesto::factory()->create()]);

    $this->actingAs($admin)->get('/pedidos')->assertInertia(
        fn ($page) => $page->component('pedidos/Index')->has('pedidos', 1)
    );
});

it('prohibe a un mecanico ver los pedidos', function () {
    $mecanico = User::factory()->create(['rol' => 'mecanico']);

    $this->actingAs($mecanico)->get('/pedidos')->assertForbidden();
});

it('manda al cliente al portal si intenta abrir los pedidos del taller', function () {
    $this->actingAs(clienteConPortal())->get('/pedidos')->assertRedirect('/portal');
});

it('permite a un admin marcar un pedido como atendido', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $pedido = pedidoDe(clienteConPortal());

    $this->actingAs($admin)
        ->patch("/pedidos/{$pedido->id}", ['estado' => 'atendido'])
        ->assertRedirect();

    expect($pedido->refresh()->estado)->toBe('atendido');
});

it('rechaza un estado invalido en un pedido', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $pedido = pedidoDe(clienteConPortal());

    $this->actingAs($admin)
        ->patch("/pedidos/{$pedido->id}", ['estado' => 'inventado'])
        ->assertSessionHasErrors('estado');
});
