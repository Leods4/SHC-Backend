<?php

namespace App\Policies;

use App\Models\Curso;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class CursoPolicy
{
    use HandlesAuthorization;

    /**
     * Permite que apenas Admin/Secretaria (Staff) gerenciem cursos.
     */
    private function isStaff(Usuario $usuario): bool
    {
        return $usuario->isStaff(); // (isStaff() = Admin ou Secretaria)
    }

    public function viewAny(Usuario $usuario): bool
    {
        return $this->isStaff($usuario); // [cite: 109]
    }

    public function view(Usuario $usuario, Curso $curso): bool
    {
        return $this->isStaff($usuario); // [cite: 109]
    }

    public function create(Usuario $usuario): bool
    {
        return $this->isStaff($usuario); // [cite: 109]
    }

    public function update(Usuario $usuario, Curso $curso): bool
    {
        return $this->isStaff($usuario); // [cite: 110]
    }

    public function delete(Usuario $usuario, Curso $curso): bool
    {
        return $this->isStaff($usuario); // [cite: 110]
    }
}