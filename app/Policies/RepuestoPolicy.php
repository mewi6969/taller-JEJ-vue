<?php

namespace App\Policies;

use App\Models\Repuesto;
use App\Models\User;

class RepuestoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function view(User $user, Repuesto $repuesto): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function create(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function update(User $user, Repuesto $repuesto): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function delete(User $user, Repuesto $repuesto): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }
}
