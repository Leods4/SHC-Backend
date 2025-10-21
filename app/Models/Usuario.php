<?php

namespace App\Models;

// Imports dos Enums
use App\Enums\RoleUsuario; // Ajuste o namespace se for diferente
use App\Enums\StatusCertificado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * O nome da tabela associada ao modelo.
     * (Opcional se o nome da tabela for 'usuarios')
     *
     * @var string
     */
    protected $table = 'usuarios'; // [cite: 4] (Implícito)

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'senha',
        'matricula', // 
        'data_nascimento', // 
        'tipo', // 
        'dados_adicionais', // 
        'curso_id', // 
        'fase', // 
    ];

    /**
     * Os atributos que devem ser ocultados para serialização.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'senha',
        'remember_token',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'senha' => 'hashed',
        'data_nascimento' => 'date', // 
        'tipo' => RoleUsuario::class, // [cite: 5, 31] (Assumindo que RoleUsuario é o Enum 'TipoUsuario')
        'dados_adicionais' => 'json', // 
    ];

    /**
     * Define o relacionamento com o Curso que o usuário pertence.
     */
    public function curso()
    {
        // Um Usuario pertence a um Curso 
        return $this->belongsTo(Curso::class, 'curso_id'); // 
    }

    /**
     * Define o relacionamento com os Certificados do usuário.
     */
    public function certificados()
    {
        // Um Usuario pode ter vários Certificados 
        return $this->hasMany(Certificado::class, 'usuario_id');
    }

    /**
     * Define o relacionamento com os Cursos que este usuário coordena.
     * (Baseado no diagrama )
     */
    public function cursosCoordenados()
    {
        // A implementação exata depende de como você armazena coordenadores.
        // Se for uma tabela pivot ('curso_usuario'  com um papel):
        // return $this->belongsToMany(Curso::class, 'curso_usuario', 'usuario_id', 'curso_id')->wherePivot('papel', 'coordenador');
        
        // Se 'curso_usuario' for apenas para alunos, talvez você tenha uma coluna 'coordenador_id' em Cursos?
        // Ajuste conforme sua estrutura de tabela.
        return $this->hasMany(Curso::class, 'coordenador_id'); // Suposição
    }

    /**
     * Define o relacionamento polimórfico com o Histórico de Alterações.
     */
    public function historico()
    {
        // Relacionamento polimórfico [cite: 4, 35]
        return $this->morphMany(HistoricoAlteracao::class, 'historicoable');
    }

    /**
     * Define o relacionamento para buscar o histórico pelo qual este usuário foi responsável.
     */
    public function historicoResponsavel()
    {
        // Um Usuario é responsável por várias alterações 
        return $this->hasMany(HistoricoAlteracao::class, 'responsavel_id'); // [cite: 34]
    }
    
    /**
     * (Funcionalidade) Calcula o total de horas validadas para este usuário.
     * [cite: 22, 31]
     */
    public function calcularHorasValidadas(): int
    {
        // [cite: 22]
        return $this->certificados()
                    ->where('status', StatusCertificado::APROVADO) // [cite: 6, 22]
                    ->sum('horas_validadas'); // [cite: 22, 33]
    }
}