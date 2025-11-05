<?php

namespace App\Observers;

use App\Models\Certificado;
use App\Observers\Traits\RegistraHistorico;

class CertificadoObserver
{
    use RegistraHistorico;

    public function created(Certificado $certificado): void
    {
        $this->registrar($certificado, 'CREATED');
    }

    public function updated(Certificado $certificado): void
    {
        $this->registrar($certificado, 'UPDATED'); // Inclui avaliações
    }

    public function deleted(Certificado $certificado): void
    {
        $this->registrar($certificado, 'DELETED');
    }
}