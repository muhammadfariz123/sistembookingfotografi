<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Financial Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/financial', [FinancialController::class, 'index'])
        ->name('financial.index');

    /*
    |--------------------------------------------------------------------------
    | Service Types
    |--------------------------------------------------------------------------
    */
    Route::resource('service-types', ServiceTypeController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Bookings
    |--------------------------------------------------------------------------
    */
    Route::get('/bookings',                [BookingController::class, 'index'])
        ->name('bookings.index');
    Route::post('/bookings',               [BookingController::class, 'store'])
        ->name('bookings.store');
    Route::put('/bookings/{booking}',      [BookingController::class, 'update'])
        ->name('bookings.update');
    Route::delete('/bookings/{booking}',   [BookingController::class, 'destroy'])
        ->name('bookings.destroy');

});

require __DIR__ . '/auth.php';