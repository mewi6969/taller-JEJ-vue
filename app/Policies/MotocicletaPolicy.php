<?php

namespace App\Policies;

use App\Models\Motocicleta;
use App\Models\User;

class MotocicletaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function view(User $user, Motocicleta $motocicleta): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function create(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function update(User $user, Motocicleta $motocicleta): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function delete(User $user, Motocicleta $motocicleta): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }
}
