<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\RestablecerContrasenaNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class RecuperarContrasenaTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rol): User
    {
        return User::factory()->create([
            'rol' => $rol,
            'password' => 'ClaveVieja123',
        ]);
    }

    public function test_la_pantalla_de_recuperacion_carga(): void
    {
        $this->get('/olvide-contrasena')->assertOk();
    }

    public function test_el_cliente_recibe_el_enlace(): void
    {
        Notification::fake();
        $cliente = $this->usuario('cliente');

        $this->post('/olvide-contrasena', ['email' => $cliente->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($cliente, RestablecerContrasenaNotification::class);
    }

    public function test_el_personal_no_recibe_enlace(): void
    {
        Notification::fake();

        foreach (['admin', 'mecanico', 'recepcionista'] as $rol) {
            $usuario = $this->usuario($rol);

            $this->post('/olvide-contrasena', ['email' => $usuario->email])
                ->assertSessionHas('status');
        }

        Notification::assertNothingSent();
    }

    public function test_un_correo_inexistente_recibe_el_mismo_mensaje(): void
    {
        Notification::fake();

        $this->post('/olvide-contrasena', ['email' => 'nadie@ejemplo.com'])
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_el_cliente_cambia_su_contrasena_con_un_token_valido(): void
    {
        $cliente = $this->usuario('cliente');
        $token = Password::broker()->createToken($cliente);

        $this->post('/restablecer-contrasena', [
            'token' => $token,
            'email' => $cliente->email,
            'password' => 'ClaveNueva123',
            'password_confirmation' => 'ClaveNueva123',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('ClaveNueva123', $cliente->fresh()->password));
    }

    public function test_un_token_invalido_no_cambia_la_contrasena(): void
    {
        $cliente = $this->usuario('cliente');

        $this->post('/restablecer-contrasena', [
            'token' => 'token-falso',
            'email' => $cliente->email,
            'password' => 'ClaveNueva123',
            'password_confirmation' => 'ClaveNueva123',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('ClaveVieja123', $cliente->fresh()->password));
    }

    public function test_el_personal_no_puede_restablecer_aunque_tenga_token(): void
    {
        $admin = $this->usuario('admin');
        $token = Password::broker()->createToken($admin);

        $this->post('/restablecer-contrasena', [
            'token' => $token,
            'email' => $admin->email,
            'password' => 'ClaveNueva123',
            'password_confirmation' => 'ClaveNueva123',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('ClaveVieja123', $admin->fresh()->password));
    }

    public function test_la_confirmacion_debe_coincidir(): void
    {
        $cliente = $this->usuario('cliente');
        $token = Password::broker()->createToken($cliente);

        $this->post('/restablecer-contrasena', [
            'token' => $token,
            'email' => $cliente->email,
            'password' => 'ClaveNueva123',
            'password_confirmation' => 'otra-cosa',
        ])->assertSessionHasErrors('password');
    }
}
