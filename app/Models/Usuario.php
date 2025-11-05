<?php

namespace App\Models;

use App\Models\Traits\HasHistorico; //
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // [cite: 22, 83]
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // [cite: 82]

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasHistorico; //;

    // ... (propriedades $table, $fillable, $hidden, $casts)

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos (Adições do Módulo 2)
    |--------------------------------------------------------------------------
    */
    
    /**
     * Relacionamento: Usuário pertence a um Papel (Role)
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relacionamento: Usuário pertence a um Curso (se Aluno ou Coord.)
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Relacionamento: Usuário (Aluno) possui muitos Certificados
     * [cite: 50]
     */
    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'aluno_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de Papéis (Roles)
    |--------------------------------------------------------------------------
    */

    public function isAluno(): bool
    {
        return $this->role?->nome === 'ALUNO';
    }

    public function isCoordenador(): bool
    {
        return $this->role?->nome === 'COORDENADOR';
    }

    public function isSecretaria(): bool
    {
        return $this->role?->nome === 'SECRETARIA';
    }

    public function isAdmin(): bool
    {
        return $this->role?->nome === 'ADMINISTRADOR';
    }

    /**
     * Verifica se o usuário tem um papel administrativo
     */
    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isSecretaria();
    }

    /*
    |--------------------------------------------------------------------------
    | Lógica de Negócio (Otimização do Módulo 7)
    |--------------------------------------------------------------------------
    */

    /**
     * Carrega o progresso de horas (para otimização do endpoint /me)
     * [cite: 30, 117]
     */
    public function loadProgresso(): void
    {
        if ($this->isAluno()) {
            $totalHoras = $this->certificados()
                ->whereIn('status', [
                    Certificado::STATUS_APROVADO, 
                    Certificado::STATUS_APROVADO_RESSALVAS
                ])
                ->sum('horas_validadas'); // [cite: 29]
            
            $this->setAttribute('progresso_horas', (int) $totalHoras);
            $this->setAttribute('curso_horas_necessarias', $this->curso?->horas_necessarias ?? 0);
        }
    }
}