<?php

namespace App\Providers;

use App\Models\Certificado; // Importar
use App\Models\Usuario; // Importar
use App\Observers\CertificadoObserver; // Importar
use App\Observers\UsuarioObserver; // Importar
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Usuario::observe(UsuarioObserver::class);
        Certificado::observe(CertificadoObserver::class);
    }
}
