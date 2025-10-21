<?php

namespace App\Enums;

/**
 * Define os tipos (papéis) de usuários do sistema 
 */
enum RoleUsuario: string
{
    case ALUNO = 'ALUNO'; // 
    case COORDENADOR = 'COORDENADOR'; // 
    case SECRETARIA = 'SECRETARIA'; // 
    case ADMINISTRADOR = 'ADMINISTRADOR'; // 
}