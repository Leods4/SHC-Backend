<?php

namespace App\Observers;

use App\Models\Certificado;
use App\Models\HistoricoAlteracao;
use Illuminate\Support\Facades\Auth;

class CertificadoObserver
{
    /**
     * Lida com o evento "created" (criado) do Certificado.
     */
    public function created(Certificado $certificado): void
    {
        $this->logHistorico($certificado, 'Certificado Criado/Submetido', $certificado->toArray());
    }

    /**
     * Lida com o evento "updated" (atualizado) do Certificado.
     */
    public function updated(Certificado $certificado): void
    {
        $changes = $certificado->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        // Observação inteligente para quando um certificado é avaliado
        $observacao = 'Certificado Atualizado';
        if (isset($changes['status'])) {
            $observacao = 'Certificado Avaliado: ' . $changes['status'];
        }

        $this->logHistorico($certificado, $observacao, $changes);
    }

    /**
     * Lida com o evento "deleted" (deletado) do Certificado.
     */
    public function deleted(Certificado $certificado): void
    {
        $this->logHistorico($certificado, 'Certificado Deletado', $certificado->toArray());
    }

    /**
     * Função auxiliar para criar o registro de histórico.
     */
    protected function logHistorico(Certificado $certificado, string $observacao, array $alteracao): void
    {
        $certificado->historico()->create([
            'responsavel_id' => Auth::id(),
            'observacao' => $observacao,
            'alteracao' => $alteracao,
            'data_alteracao' => now(),
        ]);
    }
}