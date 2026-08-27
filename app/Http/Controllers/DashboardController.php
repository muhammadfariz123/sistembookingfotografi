<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\CompanySetting;
use App\Models\ServiceCategory;
use App\Models\Booking;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan Kerangka Halaman Dashboard (View).
     * Data Booking sebenarnya akan dimuat (di-fetch) secara asinkron via AJAX 
     * oleh komponen-komponen di dalam View ini.
     */
    public function index()
    {
        $userId = Auth::id();
        $services = ServiceType::where('user_id', $userId)
            ->orderBy('name')
            ->get();

        $bookingController = new \App\Http\Controllers\BookingController();
        $initialData = $bookingController->getBookingData();

        // 1. Lengkapi Pengaturan Toko
        $hasSettings = CompanySetting::where('user_id', $userId)
            ->whereNotNull('company_name')
            ->exists();

        // 2. Tambah Kategori & Portofolio
        $hasCategory = ServiceCategory::where('user_id', $userId)->exists();

        // 3. Tambah Layanan / Paket
        $hasService = count($services) > 0;

        // 4. Buat Data Booking
        $hasBooking = Booking::where('user_id', $userId)->exists();

        // 5. Memantau Daftar Booking (Ada booking dengan status 'Dijadwalkan' atau 'Selesai')
        $hasConfirmedBooking = Booking::where('user_id', $userId)
            ->whereIn('status', ['Dijadwalkan', 'Selesai'])
            ->exists();

        // 6. Memantau Data Transaksi
        $hasTransaction = PaymentTransaction::where('user_id', $userId)->exists();

        $checklist = [
            'settings' => $hasSettings,
            'categories' => $hasCategory,
            'services' => $hasService,
            'bookings' => $hasBooking,
            'confirmed_bookings' => $hasConfirmedBooking,
            'transactions' => $hasTransaction,
        ];

        // Jika semua checklist sudah true, maka $showOnboarding = false
        $showOnboarding = !($hasSettings && $hasCategory && $hasService && $hasBooking && $hasConfirmedBooking && $hasTransaction);

        return view('dashboard', [
            'services' => $services,
            'initialSummary' => $initialData['summary'],
            'checklist' => $checklist,
            'showOnboarding' => $showOnboarding,
        ]);
    }
}