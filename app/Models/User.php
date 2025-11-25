<?php

namespace App\Models;

use App\Enums\TipoUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'cpf',
        'data_nascimento',
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
        'data_nascimento' => 'date',
    ];

    // --------------------------------------------------------
    // 🔥 BOOT: Cria a senha automaticamente se não enviada
    // --------------------------------------------------------
    protected static function booted()
    {
        static::creating(function (User $user) {
            if (empty($user->password) && !empty($user->data_nascimento)) {
                // Formata data BR: DDMMAAAA
                $user->password = $user->data_nascimento->format('dmY');
            }
        });
    }

    // --------------------------------------------------------
    // 🔥 Mutator explícito (opcional, mas seguro)
    // Garante que password sempre seja string antes do hash
    // --------------------------------------------------------
    public function setPasswordAttribute($value)
    {
        // Laravel já faz hash automaticamente (por causa do cast)
        $this->attributes['password'] = $value;
    }

    // --------------------------------------------------------
    // Relacionamentos
    // --------------------------------------------------------

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function certificadosSubmetidos(): HasMany
    {
        return $this->hasMany(Certificado::class, 'aluno_id');
    }

    public function certificadosAvaliados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'coordenador_id');
    }

    // --------------------------------------------------------
    // Helpers
    // --------------------------------------------------------

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
