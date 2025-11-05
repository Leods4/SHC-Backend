<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; //

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'limite_horas',
    ];

    // O relacionamento +certificados() [cite: 49] será adicionado 
    // quando o Modelo Certificado for criado no Módulo 4.
    public function certificados(): HasMany //
    {
        return $this->hasMany(Certificado::class);
    }
}