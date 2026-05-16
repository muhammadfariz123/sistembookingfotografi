<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Bootstrap Application
|--------------------------------------------------------------------------
| Konfigurasi utama aplikasi Laravel TPS Rozi Photography.
| Sistem menggunakan arsitektur MVC dan autentikasi admin internal.
|--------------------------------------------------------------------------
*/

return Application::configure(
    basePath: dirname(__DIR__)
)

->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
)

->withMiddleware(function (Middleware $middleware): void {

    // Middleware custom dapat ditambahkan di sini jika diperlukan

})

->withExceptions(function (Exceptions $exceptions): void {

    // Custom exception handler

})

->create();