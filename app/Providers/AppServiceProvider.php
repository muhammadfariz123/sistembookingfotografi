<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// WAJIB TAMBAHKAN BARIS INI DI ATAS:
use Illuminate\Support\Facades\URL;

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
        // PAKSA SEMUA LINK ASSET & URL MENGGUNAKAN HTTPS JIKA DI SERVER
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}