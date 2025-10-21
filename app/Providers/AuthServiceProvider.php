<?php

namespace App\Providers;

// 1. IMPORTE OS MODELOS E POLICIES
use App\Models\Usuario;
use App\Policies\UsuarioPolicy;
use App\Models\Curso;
use App\Policies\CursoPolicy;
use App\Models\Certificado;
use App\Policies\CertificadoPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 2. ADICIONE SEUS MAPEAMENTOS AQUI
        Usuario::class => UsuarioPolicy::class,
        Curso::class => CursoPolicy::class,
        Certificado::class => CertificadoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // O Laravel pode descobrir Policies automaticamente, 
        // mas registrá-las explicitamente (acima) é mais claro.
    }
}