<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CompanySetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Tampilkan halaman Bukti Pembayaran (Invoice)
     */
    public function show(Booking $booking)
    {
        // Load relasi ke layanan, transaksi riwayat, dan data admin/user
        $booking->load(['serviceType', 'transactions', 'user']);

        // Ambil data profil studio/admin
        $company = CompanySetting::where('user_id', $booking->user_id)->first();

        // Generate kembali Kode Booking
        $bookingCode = 'BKG-' . Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));

        return view('booking.invoice', compact('booking', 'company', 'bookingCode'));
    }
}