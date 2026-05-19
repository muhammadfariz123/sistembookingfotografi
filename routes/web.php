<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PublicBookingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});


// ── Public booking routes (tidak perlu login) ─────────────────
Route::get('/booking/{ownerId}', [PublicBookingController::class, 'show'])
    ->name('booking.public.show');
Route::post('/booking/{ownerId}', [PublicBookingController::class, 'store'])
    ->name('booking.public.store');
// ── Ganti {booking} → {bookingId} agar tidak konflik model binding ──
Route::get('/booking/{ownerId}/invoice/{bookingId}', [PublicBookingController::class, 'invoice'])
    ->name('booking.public.invoice');
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
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    Route::post('/financial/income', [FinancialController::class, 'storeIncome'])->name('financial.income.store');
    Route::post('/financial/expense', [FinancialController::class, 'storeExpense'])->name('financial.expense.store');
    Route::delete('/financial/income/{income}', [FinancialController::class, 'destroyIncome'])->name('financial.income.destroy');
    Route::delete('/financial/expense/{expense}', [FinancialController::class, 'destroyExpense'])->name('financial.expense.destroy');
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
    Route::get('/bookings', [BookingController::class, 'index'])
        ->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])
        ->name('bookings.store');
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])
        ->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])
        ->name('bookings.destroy');

    /*
    |--------------------------------------------------------------------------
    | Company Settings
    |--------------------------------------------------------------------------
    */
    // Company Setting
    Route::get('/company-setting', [CompanySettingController::class, 'show'])->name('company-setting.show');
    Route::post('/company-setting', [CompanySettingController::class, 'store'])->name('company-setting.store');

    // invoice
    Route::get('/invoices/{booking}', [InvoiceController::class, 'show'])->name('invoice.show');
});

require __DIR__ . '/auth.php';