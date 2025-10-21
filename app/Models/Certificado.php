<?php

namespace App\Models;

// Imports dos Enums
use App\Enums\CategoriaCertificado;
use App\Enums\StatusCertificado;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo.
     * (Opcional se o nome da tabela for 'certificados')
     *
     * @var string
     */
    protected $table = 'certificados'; // (Implícito)

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_id',
        'categoria',
        'status',
        'carga_horaria_solicitada',
        'horas_validadas',
        'nome_certificado',
        'instituicao',
        'data_emissao',
        'observacao',
        'arquivo', // [cite: 20, 33]
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'categoria' => CategoriaCertificado::class, // 
        'status' => StatusCertificado::class,       // 
        'data_emissao' => 'date',                  // 
        'carga_horaria_solicitada' => 'integer',
        'horas_validadas' => 'integer',
    ];

    /**
     * Define o relacionamento com o Usuário (requerente) do certificado.
     */
    public function requerente()
    {
        // Um Certificado pertence a um Usuário
        return $this->belongsTo(Usuario::class, 'usuario_id'); // 
    }

    /**
     * Define o relacionamento polimórfico com o Histórico de Alterações.
     */
    public function historico()
    {
        // Relacionamento polimórfico
        return $this->morphMany(HistoricoAlteracao::class, 'historicoable'); // [cite: 4, 33]
    }
}