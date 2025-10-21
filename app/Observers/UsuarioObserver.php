<?php

namespace App\Observers;

use App\Models\Usuario;
use App\Models\HistoricoAlteracao;
use Illuminate\Support\Facades\Auth;

class UsuarioObserver
{
    /**
     * Lida com o evento "created" (criado) do Usuário.
     */
    public function created(Usuario $usuario): void
    {
        $this->logHistorico($usuario, 'Usuário Criado', $usuario->toArray());
    }

    /**
     * Lida com o evento "updated" (atualizado) do Usuário.
     */
    public function updated(Usuario $usuario): void
    {
        $changes = $usuario->getChanges();
        
        // Remove 'updated_at' para não poluir o log
        unset($changes['updated_at']);

        // Oculta a senha do log
        if (isset($changes['senha'])) {
            $changes['senha'] = '******** (Atualizada)';
        }

        // Só grava no log se algo realmente mudou
        if (!empty($changes)) {
            $this->logHistorico($usuario, 'Usuário Atualizado', $changes);
        }
    }

    /**
     * Lida com o evento "deleted" (deletado) do Usuário.
     */
    public function deleted(Usuario $usuario): void
    {
        $this->logHistorico($usuario, 'Usuário Deletado', $usuario->toArray());
    }

    /**
     * Função auxiliar para criar o registro de histórico.
     */
    protected function logHistorico(Usuario $usuario, string $observacao, array $alteracao): void
    {
        // Usa o relacionamento polimórfico
        $usuario->historico()->create([
            'responsavel_id' => Auth::id(), // Pega o usuário logado que fez a ação
            'observacao' => $observacao,
            'alteracao' => $alteracao,
            'data_alteracao' => now(),
        ]);
    }
}