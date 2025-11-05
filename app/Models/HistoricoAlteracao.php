<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HistoricoAlteracao extends Model
{
    use HasFactory;

    // Tabela personalizada
    protected $table = 'historico_alteracoes';

    // Desativa timestamps 'created_at' e 'updated_at'
    public $timestamps = false;

    protected $fillable = [
        'responsavel_id',
        'historicoable_type',
        'historicoable_id',
        'acao',
        'alteracao_antes',
        'alteracao_depois',
        'observacao',
        'data_alteracao',
    ];

    protected $casts = [
        'alteracao_antes' => 'array',
        'alteracao_depois' => 'array',
        'data_alteracao' => 'datetime',
    ];

    /**
     * Relacionamento: O responsável pela alteração
     */
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    /**
     * Relacionamento: O item que foi alterado (polimórfico)
     */
    public function historicoable(): MorphTo
    {
        return $this->morphTo();
    }
}