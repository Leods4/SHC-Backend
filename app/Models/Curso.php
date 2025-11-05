<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'horas_necessarias',
    ];

    /**
     * Define o relacionamento com Usuários (Alunos e Coordenadores) [cite: 49]
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }
}