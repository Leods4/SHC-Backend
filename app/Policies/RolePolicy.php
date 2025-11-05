<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Permite que qualquer usuário autenticado veja a lista de papéis.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return true; // Todos autenticados podem ver
    }
}