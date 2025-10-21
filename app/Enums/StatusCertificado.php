<?php

namespace App\Enums;

/**
 * Define os status possíveis de um certificado [cite: 6, 29]
 */
enum StatusCertificado: string
{
    case ENTREGUE = 'ENTREGUE'; // [cite: 6, 29]
    case APROVADO = 'APROVADO'; // [cite: 6, 29]
    case REPROVADO = 'REPROVADO'; // [cite: 6, 29]
    case APROVADO_COM_RESSALVAS = 'APROVADO_COM_RESSALVAS'; // [cite: 30]
}