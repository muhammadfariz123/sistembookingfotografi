<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * READ: Mengambil semua data Booking beserta Ringkasannya (Summary).
     * Fungsi ini diakses via AJAX oleh komponen dashboard (Summary Cards & Tabel).
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            
            // 1. Ambil semua data booking berdasarkan user yang login (terbaru di atas)
            $bookings = Booking::where('user_id', Auth::id())
                ->with('serviceType')
                ->latest()
                ->get();

            // 2. Mapping data untuk dikirim sebagai JSON (Frontend)
            $mappedBookings = $bookings->map(function ($booking) {
                return [
                    'id'               => $booking->id,
                    'client_name'      => $booking->client_name,
                    'client_contact'   => $booking->client_contact,
                    'client_address'   => $booking->client_address,
                    'service_type'     => $booking->serviceType ? [
                        'id'          => $booking->serviceType->id,
                        'name'        => $booking->serviceType->name,
                        'description' => $booking->serviceType->description,
                        'price'       => $booking->serviceType->price,
                    ] : null,
                    'service_type_id'  => $booking->service_type_id,
                    
                    // Komponen Keuangan
                    'unit_price'       => (int) $booking->unit_price,
                    'discount_percent' => (float) $booking->discount_percent,
                    'discount_amount'  => (int) $booking->discount_amount,
                    'subtotal'         => (int) $booking->subtotal,
                    'total'            => (int) $booking->total,
                    'paid_amount'      => (int) $booking->paid_amount,
                    'remaining'        => (int) $booking->remaining,
                    
                    // Status
                    'payment_status'   => $booking->payment_status,
                    'status'           => $booking->status,
                    
                    // Waktu & Catatan
                    'booking_date'     => $booking->booking_date?->format('Y-m-d'),
                    'start_date'       => $booking->start_date?->format('Y-m-d'),
                    'end_date'         => $booking->end_date?->format('Y-m-d'),
                    'booking_time'     => $booking->booking_time,
                    'notes'            => $booking->notes,
                    'created_at'       => $booking->created_at,
                    'updated_at'       => $booking->updated_at,
                ];
            });

            // 3. Menghitung Ringkasan (Summary) berdasarkan Collection yang sudah di-fetch
            $summary = [
                'total'       => $bookings->count(),
                'dijadwalkan' => $bookings->where('status', 'Dijadwalkan')->count(),
                'selesai'     => $bookings->where('status', 'Selesai')->count(),
                'dibatalkan'  => $bookings->where('status', 'Dibatalkan')->count(),
                'belum_bayar' => $bookings->where('payment_status', 'Belum Bayar')->count(),
                'dp'          => $bookings->where('payment_status', 'Down Payment')->count(),
                'lunas'       => $bookings->where('payment_status', 'Lunas')->count(),
            ];

            return response()->json([
                'data'    => $mappedBookings,
                'summary' => $summary,
            ]);
        }

        // Fallback: Jika diakses biasa lewat URL (walau tidak dipakai di arsitektur saat ini)
        return view('bookings.index');
    }

    /**
     * VIEW: Menampilkan Form Tambah Booking Baru (Mandiri)
     */
    public function create()
    {
        $serviceTypes = ServiceType::where('user_id', Auth::id())->get();
        return view('bookings.form', compact('serviceTypes'));
    }

    /**
     * VIEW: Menampilkan Form Edit Booking (Mandiri)
     */
    public function edit(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $serviceTypes = ServiceType::where('user_id', Auth::id())->get();
        return view('bookings.form', compact('booking', 'serviceTypes'));
    }

    /**
     * CREATE: Menyimpan data Booking baru ke database
     * [CORE TPS] Di sinilah sistem menjalankan Rumus Perhitungan Keuangan (Processing)
     */
    public function store(BookingRequest $request)
    {
        $validated = $request->validated();

        // Pengecekan Keamanan: Pastikan layanan yang dipilih milik admin yang bersangkutan
        ServiceType::where('id', $validated['service_type_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // [LOGIKA TPS] Memanggil fungsi perhitungan otomatis di Model Booking
        $tps = Booking::calculateTps(
            unitPrice: (int) $validated['unit_price'],
            discountPercent: (float) ($validated['discount_percent'] ?? 0),
            paidAmount: (int) ($validated['paid_amount'] ?? 0),
        );

        // Menyimpan data final ke tabel database
        $booking = Booking::create([
            'user_id'          => Auth::id(),
            'service_type_id'  => $validated['service_type_id'],
            'client_name'      => $validated['client_name'],
            'client_contact'   => $validated['client_contact'] ?? null,
            'client_address'   => $validated['client_address'] ?? null,
            'booking_date'     => $validated['booking_date'] ?? null,
            'start_date'       => $validated['start_date'] ?? null,
            'end_date'         => $validated['end_date'] ?? null,
            'booking_time'     => $validated['booking_time'] ?? null,
            'status'           => $validated['status'],
            'unit_price'       => $validated['unit_price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'paid_amount'      => $validated['paid_amount'] ?? 0,
            
            // Atribut hasil perhitungan TPS
            'subtotal'         => $tps['subtotal'],
            'discount_amount'  => $tps['discount_amount'],
            'total'            => $tps['total'],
            'remaining'        => $tps['remaining'],
            'payment_status'   => $tps['payment_status'],
            
            'notes'            => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil disimpan.',
            'data'    => $booking->load('serviceType'),
        ], 201);
    }

    /**
     * UPDATE: Memperbarui data Booking yang sudah ada
     * [CORE TPS] Perhitungan ulang dilakukan otomatis apabila ada perubahan nominal
     */
    public function update(BookingRequest $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $validated = $request->validated();

        ServiceType::where('id', $validated['service_type_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // [LOGIKA TPS] Memanggil fungsi perhitungan otomatis di Model Booking
        $tps = Booking::calculateTps(
            unitPrice: (int) $validated['unit_price'],
            discountPercent: (float) ($validated['discount_percent'] ?? 0),
            paidAmount: (int) ($validated['paid_amount'] ?? 0),
        );

        $booking->update([
            'service_type_id'  => $validated['service_type_id'],
            'client_name'      => $validated['client_name'],
            'client_contact'   => $validated['client_contact'] ?? null,
            'client_address'   => $validated['client_address'] ?? null,
            'booking_date'     => $validated['booking_date'] ?? null,
            'start_date'       => $validated['start_date'] ?? null,
            'end_date'         => $validated['end_date'] ?? null,
            'booking_time'     => $validated['booking_time'] ?? null,
            'status'           => $validated['status'],
            'unit_price'       => $validated['unit_price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'paid_amount'      => $validated['paid_amount'] ?? 0,
            
            // Atribut hasil perhitungan ulang TPS
            'subtotal'         => $tps['subtotal'],
            'discount_amount'  => $tps['discount_amount'],
            'total'            => $tps['total'],
            'remaining'        => $tps['remaining'],
            'payment_status'   => $tps['payment_status'],
            
            'notes'            => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil diperbarui.',
            'data'    => $booking->fresh()->load('serviceType'),
        ]);
    }

    /**
     * DELETE: Menghapus data Booking secara permanen
     */
    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $clientName = $booking->client_name;
        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => "Booking \"{$clientName}\" berhasil dihapus.",
        ]);
    }
}