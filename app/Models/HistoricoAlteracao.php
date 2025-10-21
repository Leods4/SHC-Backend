<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoAlteracao extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'historico_alteracoes'; // (Nome provável da tabela)

    /**
     * Indica se o modelo deve ter timestamps (created_at, updated_at).
     *
     * @var bool
     */
    public $timestamps = false; //  (Baseado no diagrama que lista 'data_alteracao' em vez de created_at/updated_at)

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'responsavel_id',       // 
        'historicoable_type',   // 
        'historicoable_id',     // 
        'alteracao',            // 
        'observacao',           // 
        'data_alteracao',       // 
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'alteracao' => 'json',       // 
        'data_alteracao' => 'date',  // 
    ];

    /**
     * Define o relacionamento com o Usuário (responsável) pela alteração.
     */
    public function responsavel()
    {
        // Um Histórico pertence a um Usuário (responsável) 
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    /**
     * Define o relacionamento polimórfico (pode ser um Usuário, Certificado, etc.).
     */
    public function historicoable()
    {
        // 
        return $this->morphTo();
    }
}