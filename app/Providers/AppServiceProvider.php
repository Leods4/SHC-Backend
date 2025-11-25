<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Certificado;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

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
        JsonResource::withoutWrapping();

        // Registra o observador
        User::observe(AuditObserver::class);
        Certificado::observe(AuditObserver::class);
    }
}
