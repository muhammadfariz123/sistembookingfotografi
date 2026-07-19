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

// ── ROOT ROUTE ──────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
});

// ── Public booking routes (tidak perlu login) ───────────────────────────
Route::get('/booking/{ownerId}', [PublicBookingController::class, 'show'])
    ->name('booking.public.show');

Route::get('/booking/{ownerId}/form/{serviceId?}', [PublicBookingController::class, 'bookingForm'])
    ->name('booking.public.form');

Route::post('/booking/{ownerId}', [PublicBookingController::class, 'store'])
    ->name('booking.public.store');

// Rute untuk TAMPILAN halaman Pembayaran (sebelumnya invoice)
Route::get('/booking/{ownerId}/pembayaran/{bookingId}', [PublicBookingController::class, 'pembayaran'])
    ->name('booking.public.pembayaran');

// Rute POST untuk MENERIMA DATA upload dari form
Route::post('/booking/{ownerId}/pembayaran/{bookingId}/upload-proof', [PublicBookingController::class, 'uploadProof'])
    ->name('booking.public.upload-proof');

// Rute GET "Penyelamat" jika customer me-refresh URL upload-proof
Route::get('/booking/{ownerId}/pembayaran/{bookingId}/upload-proof', function ($ownerId, $bookingId) {
    return redirect()->route('booking.public.pembayaran', [
        'ownerId' => $ownerId,
        'bookingId' => $bookingId,
    ]);
});

// Rute untuk TAMPILAN Payment Success
Route::get('/booking/{ownerId}/payment-success/{bookingId}', [PublicBookingController::class, 'paymentSuccess'])
    ->name('booking.public.payment-success');

Route::get('/cek-booking', [PublicBookingController::class, 'checkPage'])
    ->name('booking.check.page');

Route::get('/cek-booking/result', [PublicBookingController::class, 'checkResult'])
    ->name('booking.check.result');

Route::get('/booking/{ownerId}/service/{serviceId}', [PublicBookingController::class, 'serviceDetail'])
    ->name('booking.service.detail');

Route::get('/booking/{ownerId}/service/{serviceId}/gallery', [PublicBookingController::class, 'serviceGallery'])
    ->name('booking.service.gallery');

Route::get('/booking/{ownerId}/services', [PublicBookingController::class, 'allServices'])
    ->name('booking.services.all');

/*
|--------------------------------------------------------------------------
| Protected Routes (Hanya untuk Admin yang sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

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
    Route::resource('service-types', ServiceTypeController::class);
    Route::delete('/service-galleries/{gallery}', [ServiceTypeController::class, 'destroyGallery'])->name('service-galleries.destroy');

    /*
    |--------------------------------------------------------------------------
    | Bookings
    |--------------------------------------------------------------------------
    */
    Route::get('/bookings/export', [BookingController::class, 'export'])->name('bookings.export');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::get('/bookings/count', [BookingController::class, 'count'])->name('bookings.count');
    Route::post('/bookings/{booking}/approve-payment', [BookingController::class, 'approvePayment'])->name('bookings.approve-payment');
    Route::post('/bookings/{booking}/reject-payment', [BookingController::class, 'rejectPayment'])->name('bookings.reject-payment');

    /*
    |--------------------------------------------------------------------------
    | Company Settings
    |--------------------------------------------------------------------------
    */
    Route::get('/company-setting', [CompanySettingController::class, 'edit'])->name('company-setting.edit');
    Route::post('/company-setting', [CompanySettingController::class, 'store'])->name('company-setting.store');

    // Invoice Internal
    Route::get('/invoices/{booking}', [InvoiceController::class, 'show'])->name('invoice.show');
});

// Memuat rute autentikasi bawaan Laravel Breeze (login, logout, dll)
require __DIR__ . '/auth.php';