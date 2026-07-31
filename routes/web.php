<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// ── Public booking routes (tidak perlu login) ───────────────────────────
Route::get('/booking/{ownerId}', [PublicBookingController::class, 'show'])->name('booking.public.show');
Route::get('/booking/{ownerId}/form/{serviceId?}', [PublicBookingController::class, 'bookingForm'])->name('booking.public.form');
Route::post('/booking/{ownerId}', [PublicBookingController::class, 'store'])->name('booking.public.store');
Route::get('/booking/{ownerId}/pembayaran/{bookingId}', [PublicBookingController::class, 'pembayaran'])->name('booking.public.pembayaran');
Route::post('/booking/{ownerId}/pembayaran/{bookingId}/upload-proof', [PublicBookingController::class, 'uploadProof'])->name('booking.public.upload-proof');

Route::get('/booking/{ownerId}/pembayaran/{bookingId}/upload-proof', function ($ownerId, $bookingId) {
    return redirect()->route('booking.public.pembayaran', ['ownerId' => $ownerId, 'bookingId' => $bookingId]);
});

Route::get('/booking/{ownerId}/payment-success/{bookingId}', [PublicBookingController::class, 'paymentSuccess'])->name('booking.public.payment-success');
Route::get('/cek-booking', [PublicBookingController::class, 'checkPage'])->name('booking.check.page');
Route::get('/cek-booking/result', [PublicBookingController::class, 'checkResult'])->name('booking.check.result');
Route::get('/booking/{ownerId}/service/{serviceId}', [PublicBookingController::class, 'serviceDetail'])->name('booking.service.detail');
Route::get('/booking/{ownerId}/service/{serviceId}/gallery', [PublicBookingController::class, 'serviceGallery'])->name('booking.service.gallery');
Route::get('/booking/{ownerId}/services', [PublicBookingController::class, 'allServices'])->name('booking.services.all');

// Pindahkan Route Invoice ke sini agar Klien bisa akses Bukti Pembayaran
Route::get('/invoices/{booking}', [InvoiceController::class, 'show'])->name('invoice.show');


/*
|--------------------------------------------------------------------------
| Protected Routes (Hanya untuk Admin yang sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Financial Dashboard
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    Route::post('/financial/income', [FinancialController::class, 'storeIncome'])->name('financial.income.store');
    Route::post('/financial/expense', [FinancialController::class, 'storeExpense'])->name('financial.expense.store');
    Route::delete('/financial/income/{income}', [FinancialController::class, 'destroyIncome'])->name('financial.income.destroy');
    Route::delete('/financial/expense/{expense}', [FinancialController::class, 'destroyExpense'])->name('financial.expense.destroy');

    // Layanan & Portofolio Kategori
    Route::resource('service-categories', ServiceCategoryController::class);
    Route::delete('/category-galleries/{gallery}', [ServiceCategoryController::class, 'destroyGallery'])->name('category-galleries.destroy');
    
    // Paket Layanan
    Route::resource('service-types', ServiceTypeController::class);

    // Bookings (Admin Panel)
    Route::post('/bookings/bulk-delete', [BookingController::class, 'bulkDelete'])->name('bookings.bulkDelete');
    Route::get('/bookings/list', [BookingController::class, 'listPage'])->name('bookings.listPage');
    Route::get('/bookings/calendar', [BookingController::class, 'calendarPage'])->name('bookings.calendar');
    Route::get('/bookings/export', [BookingController::class, 'export'])->name('bookings.export');
    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/bookings/{booking}/approve-payment', [BookingController::class, 'approvePayment'])->name('bookings.approve-payment');
    Route::post('/bookings/{booking}/reject-payment', [BookingController::class, 'rejectPayment'])->name('bookings.reject-payment');
    Route::post('/bookings/{booking}/update-notes', [BookingController::class, 'updateNotes'])->name('bookings.update-notes');

    // Company Settings
    Route::get('/company-setting', [CompanySettingController::class, 'edit'])->name('company-setting.edit');
    Route::post('/company-setting', [CompanySettingController::class, 'store'])->name('company-setting.store');
});

require __DIR__ . '/auth.php';