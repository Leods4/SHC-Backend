<?php

namespace App\Providers;

use App\Models\Certificado; // Importar
use App\Models\Categoria; // Importar
use App\Models\Curso; // Importar
use App\Models\Usuario; // Importar
use App\Models\Role; // Importar
use App\Policies\CategoriaPolicy; // Importar
use App\Policies\CursoPolicy; // Importar
use App\Policies\CertificadoPolicy; // Importar
use App\Policies\UsuarioPolicy; // Importar
use App\Policies\RolePolicy; // Importar
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Mapeamento Model -> Policy [cite: 33]
        Usuario::class => UsuarioPolicy::class,
        
        // Embora o modelo 'Certificado' ainda não exista, 
        // a documentação [cite: 33] indica o mapeamento.
        // Se 'App\Models\Certificado' não existir, comente esta linha por enquanto.
        Certificado::class => CertificadoPolicy::class,

        Role::class => RolePolicy::class,

        Categoria::class => CategoriaPolicy::class, // Adicionar
        Curso::class => CursoPolicy::class,       // Adicionar
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}