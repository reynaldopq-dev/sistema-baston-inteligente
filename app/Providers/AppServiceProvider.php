<?php

namespace App\Providers;

use App\Models\Baston;
use App\Models\Beneficiario;
use App\Models\Usuario;
use App\Observers\RegistraAuditoria;
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
        Beneficiario::observe(RegistraAuditoria::class);
        Baston::observe(RegistraAuditoria::class);
        Usuario::observe(RegistraAuditoria::class);
    }
}
