<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\CompanySetting;
use App\Models\ServiceCategory;
use App\Models\Booking;
use App\Models\PaymentTransaction;
use App\Models\AdditionalIncome;
use App\Models\Expense;
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
        $hasSettings = session('onboarding_settings_viewed', false) || CompanySetting::where('user_id', $userId)
            ->whereNotNull('company_name')
            ->exists();

        // 2. Tambah Kategori & Portofolio
        $hasCategory = session('onboarding_categories_viewed', false) || ServiceCategory::where('user_id', $userId)->exists();

        // 3. Tambah Layanan / Paket
        $hasService = session('onboarding_services_viewed', false) || count($services) > 0;

        // 4. Buat Data Booking (Pantau Daftar Booking)
        $hasBooking = session('onboarding_bookings_viewed', false) || Booking::where('user_id', $userId)->exists();

        // 5. Pantau Daftar Transaksi
        $hasTransaction = session('onboarding_transactions_viewed', false) || PaymentTransaction::where('user_id', $userId)->exists();

        // 6. Kelola Jadwal di Kalender
        $hasCalendar = session('onboarding_calendar_viewed', false);

        // 7. Kelola Sesi di Papan Kerja (Workboard)
        $hasWorkboard = session('onboarding_workboard_viewed', false);

        // 8. Laporan Keuangan
        $hasFinancial = session('onboarding_financial_viewed', false);

        // 9. Unduh Excel Data
        $hasExported = session('onboarding_excel_downloaded', false);

        $checklist = [
            'settings' => $hasSettings,
            'categories' => $hasCategory,
            'services' => $hasService,
            'bookings' => $hasBooking,
            'calendar' => $hasCalendar,
            'workboard' => $hasWorkboard,
            'transactions' => $hasTransaction,
            'financial' => $hasFinancial,
            'export' => $hasExported,
        ];

        // Jika semua checklist sudah true, maka $showOnboarding = false
        $showOnboarding = !($hasSettings && $hasCategory && $hasService && $hasBooking && $hasCalendar && $hasWorkboard && $hasTransaction && $hasFinancial && $hasExported);

        return view('dashboard', [
            'services' => $services,
            'initialSummary' => $initialData['summary'],
            'checklist' => $checklist,
            'showOnboarding' => $showOnboarding,
        ]);
    }

    /**
     * Menampilkan Halaman Pusat Bantuan & Panduan Sistem secara Standalone.
     */
    public function help()
    {
        $userId = Auth::id();
        $services = ServiceType::where('user_id', $userId)
            ->orderBy('name')
            ->get();

        // Pengecekan real-time status 9 langkah
        $hasSettings = session('onboarding_settings_viewed', false) || CompanySetting::where('user_id', $userId)
            ->whereNotNull('company_name')
            ->exists();

        $hasCategory = session('onboarding_categories_viewed', false) || ServiceCategory::where('user_id', $userId)->exists();
        $hasService = session('onboarding_services_viewed', false) || count($services) > 0;
        $hasBooking = session('onboarding_bookings_viewed', false) || Booking::where('user_id', $userId)->exists();
        $hasTransaction = session('onboarding_transactions_viewed', false) || PaymentTransaction::where('user_id', $userId)->exists();

        $hasCalendar = session('onboarding_calendar_viewed', false);
        $hasWorkboard = session('onboarding_workboard_viewed', false);
        $hasFinancial = session('onboarding_financial_viewed', false);
        $hasExported = session('onboarding_excel_downloaded', false);

        $checklist = [
            'settings' => $hasSettings,
            'categories' => $hasCategory,
            'services' => $hasService,
            'bookings' => $hasBooking,
            'calendar' => $hasCalendar,
            'workboard' => $hasWorkboard,
            'transactions' => $hasTransaction,
            'financial' => $hasFinancial,
            'export' => $hasExported,
        ];

        return view('bantuan', [
            'checklist' => $checklist,
        ]);
    }
}