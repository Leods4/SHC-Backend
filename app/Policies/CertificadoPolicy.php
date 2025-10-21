<?php

namespace App\Policies;

use App\Models\Certificado;
use App\Models\Usuario;
use App\Enums\RoleUsuario;
use App\Enums\StatusCertificado;
use Illuminate\Auth\Access\Response;

class CertificadoPolicy
{
    /**
     * Define quem pode ver a lista de certificados (index).
     */
    public function viewAny(Usuario $usuario): bool
    {
        // Todos podem listar, mas o controller filtrará (Aluno só vê o seu).
        // Se quisermos ser estritos, poderíamos permitir apenas Admin/Coord/Secretaria.
        return true; 
    }

    /**
     * Define quem pode ver um certificado específico.
     */
    public function view(Usuario $usuario, Certificado $certificado): bool
    {
        // Aluno pode ver seu próprio certificado
        if ($usuario->id === $certificado->usuario_id) {
            return true;
        }

        // Admin, Coordenador ou Secretaria podem ver qualquer um
        return $usuario->tipo === RoleUsuario::ADMINISTRADOR ||
               $usuario->tipo === RoleUsuario::COORDENADOR ||
               $usuario->tipo === RoleUsuario::SECRETARIA;
    }

    /**
     * Define quem pode criar certificados.
     */
    public function create(Usuario $usuario): bool
    {
        // Apenas Alunos podem criar (submeter) certificados.
        return $usuario->tipo === RoleUsuario::ALUNO;
    }

    /**
     * Define quem pode atualizar um certificado.
     */
    public function update(Usuario $usuario, Certificado $certificado): bool
    {
        // Aluno só pode atualizar SE ele for o dono E o status for 'ENTREGUE'.
        if ($usuario->id === $certificado->usuario_id) {
            return $certificado->status === StatusCertificado::ENTREGUE;
        }

        // Admin/Coord/Secretaria podem atualizar (mas não deveriam usar esta rota, e sim a /avaliar)
        return $usuario->tipo === RoleUsuario::ADMINISTRADOR ||
               $usuario->tipo === RoleUsuario::COORDENADOR ||
               $usuario->tipo === RoleUsuario::SECRETARIA;
    }

    /**
     * Define quem pode deletar um certificado.
     */
    public function delete(Usuario $usuario, Certificado $certificado): bool
    {
        // Aluno só pode deletar SE ele for o dono E o status for 'ENTREGUE'.
        if ($usuario->id === $certificado->usuario_id) {
            return $certificado->status === StatusCertificado::ENTREGUE;
        }

        // Admin pode deletar qualquer um.
        return $usuario->tipo === RoleUsuario::ADMINISTRADOR;
    }

    /**
     * Método customizado para a rota /avaliar
     * (Corresponde à rota PATCH /avaliar)
     */
    public function avaliar(Usuario $usuario, Certificado $certificado): bool
    {
        // Apenas Coordenador, Secretaria ou Administrador podem avaliar
        return $usuario->tipo === RoleUsuario::COORDENADOR ||
               $usuario->tipo === RoleUsuario::SECRETARIA ||
               $usuario->tipo === RoleUsuario::ADMINISTRADOR;
    }
}