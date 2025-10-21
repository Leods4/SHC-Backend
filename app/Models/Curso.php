<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo.
     * (Opcional se o nome da tabela for 'cursos')
     *
     * @var string
     */
    protected $table = 'cursos'; // (Implícito)

    /**
     * Indica se o modelo deve ter timestamps (created_at, updated_at).
     *
     * @var bool
     */
    public $timestamps = true; // (Padrão, mas pode ser definido explicitamente)

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'horasNecessarias', //
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'horasNecessarias' => 'integer', //
    ];

    /**
     * Define o relacionamento com os Alunos que pertencem a este curso.
     * (Baseado no relacionamento "Um Usuario pertence a um Curso")
     */
    public function alunos()
    {
        // Um Curso tem vários Usuarios (Alunos)
        return $this->hasMany(Usuario::class, 'curso_id');
    }

    /**
     * Define o relacionamento com os Coordenadores deste curso.
     * (Baseado no diagrama "Curso 0..* -- 0..* Usuario : é coordenado por")
     */
    public function coordenadores()
    {
        // Isso assume que você tem uma tabela pivot, como 'curso_usuario'
        // ou 'curso_coordenador', para ligar cursos a usuários com papel de coordenador.
        // Se 'curso_usuario' for usada para alunos E coordenadores:
        // return $this->belongsToMany(Usuario::class, 'curso_usuario', 'curso_id', 'usuario_id')->wherePivot('papel', 'COORDENADOR');

        // Se você tiver uma coluna 'coordenador_id' na tabela 'cursos', seria:
        // return $this->belongsTo(Usuario::class, 'coordenador_id');
        
        // Vou usar a definição do diagrama (Muitos-para-Muitos)
        // Ajuste 'curso_usuario' se o nome da sua tabela pivot for outro.
        return $this->belongsToMany(Usuario::class, 'curso_usuario', 'curso_id', 'usuario_id');
    }
}