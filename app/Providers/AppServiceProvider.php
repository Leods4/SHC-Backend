<?php

namespace App\Providers;

// 1. IMPORTE OS MODELOS E OS OBSERVERS
use App\Models\Usuario;
use App\Observers\UsuarioObserver;
use App\Models\Certificado;
use App\Observers\CertificadoObserver;

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
        // 2. ADICIONE ESTAS LINHAS
        Usuario::observe(UsuarioObserver::class);
        Certificado::observe(CertificadoObserver::class);
    }
}