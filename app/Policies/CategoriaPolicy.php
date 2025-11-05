<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaPolicy
{
    use HandlesAuthorization;

    /**
     * Permite que apenas Admin/Secretaria (Staff) gerenciem categorias.
     */
    private function isStaff(Usuario $usuario): bool
    {
        return $usuario->isStaff(); // (isStaff() = Admin ou Secretaria)
    }

    public function viewAny(Usuario $usuario): bool
    {
        return $this->isStaff($usuario); // [cite: 111]
    }

    public function view(Usuario $usuario, Categoria $categoria): bool
    {
        return $this->isStaff($usuario);
    }

    public function create(Usuario $usuario): bool
    {
        return $this->isStaff($usuario); // [cite: 111]
    }

    public function update(Usuario $usuario, Categoria $categoria): bool
    {
        return $this->isStaff($usuario); // [cite: 112]
    }

    public function delete(Usuario $usuario, Categoria $categoria): bool
    {
        return $this->isStaff($usuario); // [cite: 112]
    }
}