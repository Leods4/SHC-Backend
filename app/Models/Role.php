<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    /**
     * Define o relacionamento com Usuários [cite: 49]
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }
}