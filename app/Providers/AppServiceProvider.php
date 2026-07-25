<?php

namespace App\Providers;

use App\View\Composers\SidebarComposer;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        View::composer('layouts.navigation', SidebarComposer::class);

        // PAKSA SEMUA LINK ASSET & URL MENGGUNAKAN HTTPS JIKA DI SERVER
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}