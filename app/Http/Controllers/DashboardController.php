<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\ServiceType;
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
        // Mengambil daftar layanan milik admin yang sedang login
        // (Biasanya digunakan untuk dropdown filter atau form input)
        $services = ServiceType::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('dashboard', compact('services'));
    }
}