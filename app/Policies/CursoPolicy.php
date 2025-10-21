<?php

namespace App\Policies;

use App\Models\Curso;
use App\Models\Usuario;
use App\Enums\RoleUsuario;
use Illuminate\Auth\Access\Response;

class CursoPolicy
{
    /**
     * Permite que um super-admin faça qualquer ação.
     */
    public function before(Usuario $usuario, string $ability): bool|null
    {
        if ($usuario->tipo === RoleUsuario::ADMINISTRADOR) {
            return true;
        }
        return null;
    }

    /**
     * Define quem pode ver a lista de cursos (index).
     */
    public function viewAny(Usuario $usuario): bool
    {
        // Qualquer usuário logado pode ver a lista de cursos.
        return true;
    }

    /**
     * Define quem pode ver um curso específico.
     */
    public function view(Usuario $usuario, Curso $curso): bool
    {
        // Qualquer usuário logado pode ver os detalhes de um curso.
        return true;
    }

    /**
     * Define quem pode criar cursos.
     */
    public function create(Usuario $usuario): bool
    {
        // Apenas Secretaria (e Admin, via 'before') podem criar cursos.
        return $usuario->tipo === RoleUsuario::SECRETARIA;
    }

    /**
     * Define quem pode atualizar um curso.
     */
    public function update(Usuario $usuario, Curso $curso): bool
    {
        // Coordenadores e Secretarias podem atualizar cursos.
        return $usuario->tipo === RoleUsuario::COORDENADOR ||
               $usuario->tipo === RoleUsuario::SECRETARIA;
    }

    /**
     * Define quem pode deletar um curso.
     */
    public function delete(Usuario $usuario, Curso $curso): bool
    {
        // Apenas Admin pode deletar (pego no 'before').
        return false;
    }
}
