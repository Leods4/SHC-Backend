<?php

namespace App\Models\Traits;

use App\Models\HistoricoAlteracao;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasHistorico
{
    /**
     * Define o relacionamento polimórfico com o Histórico.
     */
    public function historico(): MorphMany
    {
        return $this->morphMany(HistoricoAlteracao::class, 'historicoable');
    }
}