<?php
// app/Http/Controllers/InvoiceController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Ambil data booking + company setting untuk generate invoice
     */
    public function show(Booking $booking)
    {
        // Pastikan booking milik user yang login
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load('serviceType');

        $company = CompanySetting::where('user_id', Auth::id())->first();

        return response()->json([
            'success' => true,
            'booking' => $booking,
            'company' => $company,
        ]);
    }
}