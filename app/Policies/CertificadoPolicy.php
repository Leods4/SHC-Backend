<?php

namespace App\Policies;

use App\Models\Certificado;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificadoPolicy
{
    use HandlesAuthorization;

    /**
     * Regra: ADMIN/SECRETARIA podem pular verificações de avaliação.
     */
    public function before(Usuario $usuario, string $ability): bool|null
    {
        // ADMIN e SECRETARIA podem avaliar/ver tudo
        if ($usuario->isStaff() && in_array($ability, ['evaluate', 'view', 'viewAny'])) {
            return true;
        }
        return null;
    }

    /**
     * Permite ver a lista de certificados.
     * (Staff/Coord vêem vários, Aluno vê apenas os seus)
     */
    public function viewAny(Usuario $usuario): bool
    {
        return true; // A lógica de filtro real estará no Controller
    }

    /**
     * Permite ver um certificado específico.
     */
    public function view(Usuario $usuario, Certificado $certificado): bool
    {
        // Aluno pode ver seu próprio certificado
        if ($usuario->isAluno()) {
            return $usuario->id === $certificado->aluno_id;
        }

        // Coordenador pode ver certificados de alunos do seu curso [cite: 92]
        if ($usuario->isCoordenador()) {
            return $usuario->curso_id === $certificado->aluno?->curso_id;
        }

        return false; // Staff já foi tratado no before()
    }

    /**
     * Permite criar um certificado (Apenas Aluno)
     * [cite: 65, 91]
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->isAluno();
    }

    /**
     * Permite atualizar um certificado (Dono E status = ENTREGUE)
     * [cite: 69, 91]
     */
    public function update(Usuario $usuario, Certificado $certificado): bool
    {
        if (!$usuario->isAluno()) {
            return false;
        }
        
        return $usuario->id === $certificado->aluno_id &&
               $certificado->status === Certificado::STATUS_ENTREGUE;
    }

    /**
     * Permite deletar um certificado (Dono E status = ENTREGUE)
     * [cite: 43, 91]
     */
    public function delete(Usuario $usuario, Certificado $certificado): bool
    {
        if (!$usuario->isAluno()) {
            return false;
        }

        return $usuario->id === $certificado->aluno_id &&
               $certificado->status === Certificado::STATUS_ENTREGUE;
    }

    /**
     * Permite avaliar um certificado (ADMIN, SECRETARIA, COORDENADOR)
     * [cite: 71, 92]
     */
    public function evaluate(Usuario $usuario, Certificado $certificado): bool
    {
        // Coordenador só pode avaliar alunos do seu curso [cite: 92]
        if ($usuario->isCoordenador()) {
            return $usuario->curso_id === $certificado->aluno?->curso_id;
        }

        // Staff (Admin/Secretaria) é tratado no before()
        return $usuario->isStaff();
    }
}