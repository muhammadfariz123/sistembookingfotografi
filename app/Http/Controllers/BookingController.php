<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Ambil semua booking + summary count
     */
    // app/Http/Controllers/BookingController.php
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $bookings = Booking::where('user_id', Auth::id())
                ->with('serviceType')
                ->latest()
                ->get()
                ->map(function ($booking) {
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
                        'unit_price' => (int) $booking->unit_price,
                        'discount_percent' => (float) $booking->discount_percent,
                        'discount_amount' => (int) $booking->discount_amount,
                        'subtotal' => (int) $booking->subtotal,
                        'total' => (int) $booking->total,
                        'paid_amount' => (int) $booking->paid_amount,
                        'remaining' => (int) $booking->remaining,
                        'payment_status' => $booking->payment_status,
                        'status' => $booking->status,
                        'booking_date' => $booking->booking_date?->format('Y-m-d'),
                        'start_date' => $booking->start_date?->format('Y-m-d'),
                        'end_date' => $booking->end_date?->format('Y-m-d'),
                        'booking_time' => $booking->booking_time,
                        'notes' => $booking->notes,
                        'created_at' => $booking->created_at,
                        'updated_at' => $booking->updated_at,
                    ];
                });

            // Summary cards
            $all = Booking::where('user_id', Auth::id())->get();
            $summary = [
                'total' => $all->count(),
                'dijadwalkan' => $all->where('status', 'Dijadwalkan')->count(),
                'selesai' => $all->where('status', 'Selesai')->count(),
                'dibatalkan' => $all->where('status', 'Dibatalkan')->count(),
                'belum_bayar' => $all->where('payment_status', 'Belum Bayar')->count(),
                'dp' => $all->where('payment_status', 'Down Payment')->count(),
                'lunas' => $all->where('payment_status', 'Lunas')->count(),
            ];

            return response()->json([
                'data' => $bookings,
                'summary' => $summary,
            ]);
        }

        return view('dashboard');
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

    public function count(Request $request)
    {
        return response()->json([
            'count' => Booking::where('user_id', Auth::id())->count()
        ]);
    }
    
    public function stream(Request $request)
    {
        $userId = Auth::id();

        return response()->stream(function () use ($userId) {
            // Kirim count awal
            $lastCount = Booking::where('user_id', $userId)->count();
            echo "data: {$lastCount}\n\n";
            ob_flush();
            flush();

            // Loop — cek setiap 3 detik
            while (true) {
                sleep(3);

                // Cek koneksi masih aktif
                if (connection_aborted())
                    break;

                $currentCount = Booking::where('user_id', $userId)->count();

                // Kirim data terbaru setiap kali
                echo "data: {$currentCount}\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }
}