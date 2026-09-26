<?php

namespace App\Policies;

use App\Models\Servicio;
use App\Models\User;

class ServicioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista() || $user->esMecanico();
    }

    public function view(User $user, Servicio $servicio): bool
    {
        if ($user->esAdmin() || $user->esRecepcionista()) {
            return true;
        }

        return $user->esMecanico() && $servicio->mecanico_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista() || $user->puede_crear_servicios;
    }

    public function update(User $user, Servicio $servicio): bool
    {
        if ($user->esAdmin() || $user->esRecepcionista()) {
            return true;
        }

        return $user->esMecanico() && $servicio->mecanico_id === $user->id;
    }

    public function delete(User $user, Servicio $servicio): bool
    {
        if ($user->esAdmin()) {
            return true;
        }

        return $user->esMecanico() && $servicio->mecanico_id === $user->id;
    }
}
