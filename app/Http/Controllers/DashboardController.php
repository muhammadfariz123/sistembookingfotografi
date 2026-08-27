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

        // 6. Kelola Jadwal di Kalender
        $hasCalendar = Booking::where('user_id', $userId)
            ->where('status', 'Dijadwalkan')
            ->exists();

        // 7. Kelola Sesi di Papan Kerja (Workboard)
        $hasWorkboard = Booking::where('user_id', $userId)
            ->where(function($q) {
                $q->whereNotNull('link_hasil')
                  ->orWhereNotNull('link_folder_kerja')
                  ->orWhereNotNull('link_original');
            })->exists();

        // 8. Catat Transaksi Klien
        $hasTransaction = PaymentTransaction::where('user_id', $userId)->exists();

        // 9. Laporan Keuangan
        $hasFinancial = AdditionalIncome::where('user_id', $userId)->exists() || Expense::where('user_id', $userId)->exists();

        // 10. Unduh Excel Data
        $hasExported = session('onboarding_excel_downloaded', false);

        $checklist = [
            'settings' => $hasSettings,
            'categories' => $hasCategory,
            'services' => $hasService,
            'bookings' => $hasBooking,
            'confirmed_bookings' => $hasConfirmedBooking,
            'calendar' => $hasCalendar,
            'workboard' => $hasWorkboard,
            'transactions' => $hasTransaction,
            'financial' => $hasFinancial,
            'export' => $hasExported,
        ];

        // Jika semua checklist sudah true, maka $showOnboarding = false
        $showOnboarding = !($hasSettings && $hasCategory && $hasService && $hasBooking && $hasConfirmedBooking && $hasCalendar && $hasWorkboard && $hasTransaction && $hasFinancial && $hasExported);

        return view('dashboard', [
            'services' => $services,
            'initialSummary' => $initialData['summary'],
            'checklist' => $checklist,
            'showOnboarding' => $showOnboarding,
        ]);
    }
}