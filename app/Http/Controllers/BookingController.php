<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingExport;

class BookingController extends Controller
{
    /**
     * READ: Mengambil semua data Booking beserta Ringkasannya (Summary).
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {

            // 1. Ambil semua data booking berdasarkan user yang login
            $bookings = Booking::where('user_id', Auth::id())
                ->with('serviceType')
                ->latest()
                ->get();

            // 2. Mapping data untuk dikirim sebagai JSON (Frontend)
            $mappedBookings = $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'client_name' => $booking->client_name,
                    'client_contact' => $booking->client_contact,
                    'client_address' => $booking->client_address,
                    'service_type' => $booking->serviceType ? [
                        'id' => $booking->serviceType->id,
                        'name' => $booking->serviceType->name,
                        'description' => $booking->serviceType->description,
                        'price' => $booking->serviceType->price,
                    ] : null,
                    'service_type_id' => $booking->service_type_id,

                    // Komponen Keuangan
                    'unit_price' => (int) $booking->unit_price,
                    'discount_percent' => (float) $booking->discount_percent,
                    'discount_amount' => (int) $booking->discount_amount,
                    'subtotal' => (int) $booking->subtotal,
                    'total' => (int) $booking->total,
                    'paid_amount' => (int) $booking->paid_amount,
                    'remaining' => (int) $booking->remaining,

                    // Status
                    'payment_status' => $booking->payment_status,
                    'payment_type' => $booking->payment_type,
                    'payment_proof' => $booking->payment_proof, // [PENTING] Tambahkan baris ini
                    'status' => $booking->status,

                    // Waktu & Catatan
                    'booking_date' => $booking->booking_date?->format('Y-m-d'),
                    'start_date' => $booking->start_date?->format('Y-m-d'),
                    'end_date' => $booking->end_date?->format('Y-m-d'),
                    'booking_time' => $booking->booking_time,
                    'notes' => $booking->notes,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                ];
            });

            // 3. Menghitung Ringkasan (Summary) berdasarkan Collection
            // [DIUBAH]: Menambahkan pembayaran_tertunda, proses_edit, dan merubah belum_bayar jadi pending
            $summary = [
                'total' => $bookings->count(),
                'dijadwalkan' => $bookings->where('status', 'Dijadwalkan')->count(),
                'pembayaran_tertunda' => $bookings->where('status', 'Pembayaran Tertunda')->count(),
                'proses_edit' => $bookings->where('status', 'Proses Edit')->count(),
                'selesai' => $bookings->where('status', 'Selesai')->count(),
                'dibatalkan' => $bookings->where('status', 'Dibatalkan')->count(),

                'pending' => $bookings->where('payment_status', 'Pending')->count(),
                'dp' => $bookings->where('payment_status', 'Down Payment')->count(),
                'lunas' => $bookings->where('payment_status', 'Lunas')->count(),
            ];

            return response()->json([
                'data' => $mappedBookings,
                'summary' => $summary,
            ]);
        }

        return view('bookings.index');
    }

    public function create()
    {
        $serviceTypes = ServiceType::where('user_id', Auth::id())->get();
        return view('bookings.form', compact('serviceTypes'));
    }

    public function edit(Booking $booking)
    {
        if ($booking->user_id !== Auth::id())
            abort(403);

        $serviceTypes = ServiceType::where('user_id', Auth::id())->get();
        return view('bookings.form', compact('booking', 'serviceTypes'));
    }

    // Fungsi untuk menyetujui pembayaran
    /**
     * Menyetujui Bukti Pembayaran (Konfirmasi) dari Panel Admin
     */
    public function approvePayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== \Illuminate\Support\Facades\Auth::id())
            abort(403);

        $type = strtoupper($booking->payment_type);
        $dpAmount = (int) ceil($booking->total * 0.3);

        // Menentukan nominal transaksi saat ini dan total yang sudah dibayar keseluruhan
        if ($type === 'PELUNASAN') {
            $currentPaymentAmount = $booking->remaining > 0 ? $booking->remaining : ($booking->total - $dpAmount);
            $totalPaid = $booking->total;
            $paymentStatus = 'Lunas';
            $message = 'Pembayaran PELUNASAN berhasil dikonfirmasi!';
        } elseif ($type === 'LUNAS') {
            $currentPaymentAmount = $booking->total;
            $totalPaid = $booking->total;
            $paymentStatus = 'Lunas';
            $message = 'Pembayaran LUNAS berhasil dikonfirmasi!';
        } else {
            // DOWN PAYMENT (DP)
            $currentPaymentAmount = $dpAmount;
            $totalPaid = $dpAmount;
            $paymentStatus = 'Down Payment';
            $message = 'Pembayaran DP 30% berhasil dikonfirmasi!';
        }

        // Update database
        $booking->update([
            'payment_status' => $paymentStatus,
            'status' => 'Dijadwalkan',
            'paid_amount' => $totalPaid,
            'remaining' => max($booking->total - $totalPaid, 0)
        ]);

        // Generate parameter data pendukung
        $bookingCode = 'BKG-' . \Carbon\Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));
        $booking->load('serviceType'); // Load relasi paket

        // ==========================================================
        // 1. KIRIM EMAIL NOTIFIKASI INTERNAL KE ADMIN
        // ==========================================================
        try {
            $adminUser = \Illuminate\Support\Facades\Auth::user();
            \Illuminate\Support\Facades\Mail::to($adminUser->email)->send(
                new \App\Mail\AdminPaymentApprovedNotification($booking, $bookingCode, $currentPaymentAmount)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal kirim email Admin: " . $e->getMessage());
        }

        // ==========================================================
        // 2. KIRIM EMAIL KONFIRMASI KE CUSTOMER
        // ==========================================================
        if (!empty($booking->client_email)) {
            try {
                $companySetting = \App\Models\CompanySetting::where('user_id', $booking->user_id)->first();
                $companyName = $companySetting->company_name ?? \Illuminate\Support\Facades\Auth::user()->name;
                $companyPhone = $companySetting->company_phone ?? null;

                \Illuminate\Support\Facades\Mail::to($booking->client_email)->send(
                    new \App\Mail\PaymentConfirmedToCustomer($booking, $bookingCode, $companyName, $companyPhone, $currentPaymentAmount)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal kirim email konfirmasi Customer: " . $e->getMessage());
            }
        }
        // ==========================================================

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Menolak Bukti Pembayaran (Tolak) dari Panel Admin
     */
    public function rejectPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== \Illuminate\Support\Facades\Auth::id())
            abort(403);

        // Menangkap alasan input penolakan dari modal admin panel
        $reason = $request->reason ?? 'Bukti transfer tidak valid/nominal tidak sesuai.';

        // Update status pembayaran menjadi 'Ditolak' dan status jadwal menjadi 'Dibatalkan'
        $booking->update([
            'payment_status' => 'Ditolak',
            'status' => 'Dibatalkan',
            'notes' => $booking->notes . "\n\n[DITOLAK]: " . $reason
        ]);

        // ==========================================================
        // KIRIM EMAIL PEMBERITAHUAN PENOLAKAN KE CUSTOMER
        // ==========================================================
        if (!empty($booking->client_email)) {
            try {
                // Generate parameter data pendukung
                $bookingCode = 'BKG-' . \Carbon\Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));
                $companySetting = \App\Models\CompanySetting::where('user_id', $booking->user_id)->first();
                $companyName = $companySetting->company_name ?? \Illuminate\Support\Facades\Auth::user()->name;
                $companyPhone = $companySetting->company_phone ?? null;

                // Load relasi serviceType
                $booking->load('serviceType');

                \Illuminate\Support\Facades\Mail::to($booking->client_email)->send(
                    new \App\Mail\PaymentRejectedToCustomer($booking, $bookingCode, $companyName, $companyPhone, $reason)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal kirim email penolakan ke Customer: " . $e->getMessage());
            }
        }
        // ==========================================================

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran ditolak dan booking dibatalkan.'
        ]);
    }
    public function store(BookingRequest $request)
    {
        $validated = $request->validated();

        ServiceType::where('id', $validated['service_type_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $tps = Booking::calculateTps(
            unitPrice: (int) $validated['unit_price'],
            discountPercent: (float) ($validated['discount_percent'] ?? 0),
            paidAmount: (int) ($validated['paid_amount'] ?? 0),
        );

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_type_id' => $validated['service_type_id'],
            'client_name' => $validated['client_name'],
            'client_contact' => $validated['client_contact'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
            'booking_date' => $validated['booking_date'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'booking_time' => $validated['booking_time'] ?? null,
            'status' => $validated['status'],
            'unit_price' => $validated['unit_price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'paid_amount' => $validated['paid_amount'] ?? 0,

            'subtotal' => $tps['subtotal'],
            'discount_amount' => $tps['discount_amount'],
            'total' => $tps['total'],
            'remaining' => $tps['remaining'],
            'payment_status' => $tps['payment_status'],

            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil disimpan.',
            'data' => $booking->load('serviceType'),
        ], 201);
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id())
            abort(403);

        $validated = $request->validated();

        ServiceType::where('id', $validated['service_type_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $tps = Booking::calculateTps(
            unitPrice: (int) $validated['unit_price'],
            discountPercent: (float) ($validated['discount_percent'] ?? 0),
            paidAmount: (int) ($validated['paid_amount'] ?? 0),
        );

        $booking->update([
            'service_type_id' => $validated['service_type_id'],
            'client_name' => $validated['client_name'],
            'client_contact' => $validated['client_contact'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
            'booking_date' => $validated['booking_date'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'booking_time' => $validated['booking_time'] ?? null,
            'status' => $validated['status'],
            'unit_price' => $validated['unit_price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'paid_amount' => $validated['paid_amount'] ?? 0,

            'subtotal' => $tps['subtotal'],
            'discount_amount' => $tps['discount_amount'],
            'total' => $tps['total'],
            'remaining' => $tps['remaining'],
            'payment_status' => $tps['payment_status'],

            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil diperbarui.',
            'data' => $booking->fresh()->load('serviceType'),
        ]);
    }

    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== Auth::id())
            abort(403);

        $clientName = $booking->client_name;
        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => "Booking \"{$clientName}\" berhasil dihapus.",
        ]);
    }


    public function export(Request $request)
    {
        return Excel::download(new BookingExport($request->all()), 'Data_Booking.xlsx');
    }
}