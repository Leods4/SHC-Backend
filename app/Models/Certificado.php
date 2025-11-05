<?php

namespace App\Models;

use App\Models\Traits\HasHistorico; //
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificado extends Model
{
    use HasFactory, HasHistorico;

    // Constantes de Status (baseado na documentação) [cite: 48]
    public const STATUS_ENTREGUE = 'ENTREGUE';
    public const STATUS_APROVADO = 'APROVADO';
    public const STATUS_REPROVADO = 'REPROVADO';
    public const STATUS_APROVADO_RESSALVAS = 'APROVADO_COM_RESSALVAS';

    protected $fillable = [
        'aluno_id',
        'categoria_id',
        'status',
        'carga_horaria_solicitada',
        'horas_validadas',
        'nome_certificado',
        'instituicao',
        'data_emissao',
        'observacao',
        'arquivo',
    ];

    /**
     * Relacionamento: Certificado pertence a um Aluno (Usuário)
     * [cite: 51]
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aluno_id');
    }

    // O relacionamento +categoria() [cite: 51] será usado 
    // quando implementarmos o Módulo 4.
}