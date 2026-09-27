<?php

namespace App\Policies;

use App\Models\Factura;
use App\Models\User;

class FacturaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function view(User $user, Factura $factura): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function create(User $user): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function update(User $user, Factura $factura): bool
    {
        return $user->esAdmin() || $user->esRecepcionista();
    }

    public function delete(User $user, Factura $factura): bool
    {
        return $user->esAdmin();
    }
}
