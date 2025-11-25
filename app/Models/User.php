<?php

namespace App\Models;

use App\Enums\TipoUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'cpf',
        'data_nascimento', // <--- Adicionado
        'matricula',
        'password',
        'tipo',
        'avatar_url',
        'curso_id',
        'fase',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'tipo' => TipoUsuario::class,
        'password' => 'hashed',
        'data_nascimento' => 'date', // <--- Adicionado
    ];

    // Relacionamento: Usuário (Aluno/Coordenador) pertence a um Curso
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    // Relacionamento: Aluno submete muitos certificados
    public function certificadosSubmetidos(): HasMany
    {
        return $this->hasMany(Certificado::class, 'aluno_id');
    }

    // Relacionamento: Coordenador avalia muitos certificados
    public function certificadosAvaliados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'coordenador_id');
    }

    // Helper para verificar tipo
    public function isAluno(): bool
    {
        return $this->tipo === TipoUsuario::ALUNO;
    }

    public function isCoordenador(): bool
    {
        return $this->tipo === TipoUsuario::COORDENADOR;
    }

    public function isSecretaria(): bool
    {
        return $this->tipo === TipoUsuario::SECRETARIA;
    }

    public function isAdmin(): bool
    {
        return $this->tipo === TipoUsuario::ADMINISTRADOR;
    }
}
