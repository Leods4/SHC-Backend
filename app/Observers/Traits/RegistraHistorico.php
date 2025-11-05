<?php

namespace App\Observers\Traits;

use App\Models\HistoricoAlteracao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait RegistraHistorico
{
    /**
     * Registra um evento de histórico.
     *
     * @param Model $model O modelo (ex: Usuario, Certificado)
     * @param string $acao Ação (ex: 'CREATED', 'UPDATED')
     */
    protected function registrar(Model $model, string $acao): void
    {
        // Se a classe não tiver o Trait 'HasHistorico', ignora
        if (!method_exists($model, 'historico')) {
            return;
        }

        $antes = null;
        $depois = null;

        if ($acao === 'CREATED') {
            $depois = $model->getAttributes();
        } 
        elseif ($acao === 'UPDATED') {
            $antes = $model->getOriginal();
            $depois = $model->getChanges();
        }
        // Para 'DELETED' (soft ou hard), 'antes' e 'depois' podem ser nulos

        $model->historico()->create([
            'responsavel_id' => Auth::id(), // Pega o usuário autenticado
            'acao' => $acao,
            'alteracao_antes' => $antes,
            'alteracao_depois' => $depois,
            'data_alteracao' => now(),
        ]);
    }
}