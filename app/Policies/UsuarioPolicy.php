<?php

namespace App\Policies;

use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsuarioPolicy
{
    use HandlesAuthorization;

    /**
     * Regra: ADMIN/SECRETARIA pode fazer tudo.
     */
    public function before(Usuario $usuario, string $ability): bool|null
    {
        if ($usuario->isStaff()) { // isStaff() = Admin ou Secretaria
            return true;
        }
        return null; // Deixa as outras regras decidirem
    }

    /**
     * Permite ver a lista de usuários (Apenas Staff)
     */
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->isStaff();
    }

    /**
     * Permite ver um usuário específico
     * (Staff vê todos, usuário vê a si mesmo)
     */
    public function view(Usuario $usuario, Usuario $model): bool
    {
        return $usuario->id === $model->id; // [cite: 90]
    }

    /**
     * Permite criar um usuário (Apenas Staff)
     * [cite: 57]
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->isStaff();
    }

    /**
     * Permite atualizar um usuário
     * [cite: 62]
     */
    public function update(Usuario $usuario, Usuario $model): bool
    {
        // Se o usuário está tentando alterar o role_id
        if (request()->has('role_id') && request()->input('role_id') != $model->role_id) {
             // Apenas Staff pode alterar role_id [cite: 14, 91]
            return $usuario->isStaff();
        }
        
        // Usuário pode editar a si mesmo [cite: 90]
        return $usuario->id === $model->id;
    }

    /**
     * Permite desativar (soft delete) um usuário (Apenas Staff)
     * [cite: 21, 91]
     */
    public function delete(Usuario $usuario, Usuario $model): bool
    {
        // Um usuário não pode desativar a si mesmo, apenas Staff
        return $usuario->isStaff() && $usuario->id !== $model->id;
    }
    
    // ... (outros métodos como restore, forceDelete, se necessário)
}