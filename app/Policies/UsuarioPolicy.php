<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Enums\RoleUsuario;
use Illuminate\Auth\Access\Response;

class UsuarioPolicy
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
     * Define quem pode ver a lista de usuários (index).
     */
    public function viewAny(Usuario $usuario): bool
    {
        // Apenas perfis de gestão podem ver a lista completa de usuários.
        return $usuario->tipo === RoleUsuario::COORDENADOR ||
               $usuario->tipo === RoleUsuario::SECRETARIA;
    }

    /**
     * Define quem pode ver um perfil de usuário específico.
     */
    public function view(Usuario $usuario, Usuario $model): bool
    {
        // O usuário pode ver seu próprio perfil.
        if ($usuario->id === $model->id) {
            return true;
        }

        // Perfis de gestão podem ver qualquer perfil.
        return $usuario->tipo === RoleUsuario::COORDENADOR ||
               $usuario->tipo === RoleUsuario::SECRETARIA;
    }

    /**
     * Define quem pode criar usuários.
     */
    public function create(Usuario $usuario): bool
    {
        // Apenas Secretaria e Admin podem criar novos usuários (ex: matricular).
        return $usuario->tipo === RoleUsuario::SECRETARIA;
    }

    /**
     * Define quem pode atualizar um perfil de usuário.
     */
    public function update(Usuario $usuario, Usuario $model): bool
    {
        // O usuário pode atualizar seu próprio perfil.
        if ($usuario->id === $model->id) {
            return true;
        }

        // Apenas Secretaria pode atualizar outros usuários (Admin já foi pego no before).
        return $usuario->tipo === RoleUsuario::SECRETARIA;
    }

    /**
     * Define quem pode deletar um usuário.
     */
    public function delete(Usuario $usuario, Usuario $model): bool
    {
        // Apenas o Admin pode deletar usuários (pego no 'before').
        // Ninguém mais pode.
        return false;
    }
    
    /**
     * Método customizado para a rota /progresso
     */
    public function viewProgresso(Usuario $usuario, Usuario $model): bool
    {
         // O usuário pode ver seu próprio progresso.
        if ($usuario->id === $model->id) {
            return true;
        }

        // Perfis de gestão podem ver o progresso de qualquer um.
        return $usuario->tipo === RoleUsuario::COORDENADOR ||
               $usuario->tipo === RoleUsuario::SECRETARIA;
    }
}
