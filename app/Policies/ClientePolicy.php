<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function view(User $user, Cliente $cliente): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function create(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function update(User $user, Cliente $cliente): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function delete(User $user, Cliente $cliente): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }
}
