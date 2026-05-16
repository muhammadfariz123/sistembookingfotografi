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
    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('serviceType')
            ->orderBy('created_at', 'desc')
            ->get();

        // Summary untuk card
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
            'success' => true,
            'data'    => $bookings,
            'summary' => $summary,
        ]);
    }

    public function store(BookingRequest $request)
    {
        $validated = $request->validated();

        ServiceType::where('id', $validated['service_type_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $tps = Booking::calculateTps(
            unitPrice:       (int)   $validated['unit_price'],
            quantity:        (int)   $validated['quantity'],
            discountPercent: (float) ($validated['discount_percent'] ?? 0),
            paidAmount:      (int)   ($validated['paid_amount'] ?? 0),
        );

        $booking = Booking::create([
            'user_id'          => Auth::id(),
            'service_type_id'  => $validated['service_type_id'],
            'client_name'      => $validated['client_name'],
            'client_contact'   => $validated['client_contact']   ?? null,
            'client_address'   => $validated['client_address']   ?? null,
            'booking_date'     => $validated['booking_date']     ?? null,
            'start_date'       => $validated['start_date']       ?? null,
            'end_date'         => $validated['end_date']         ?? null,
            'booking_time'     => $validated['booking_time']     ?? null,
            'status'           => $validated['status'],
            'quantity'         => $validated['quantity'],
            'unit_price'       => $validated['unit_price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'paid_amount'      => $validated['paid_amount']      ?? 0,
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

    public function update(BookingRequest $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $validated = $request->validated();

        ServiceType::where('id', $validated['service_type_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $tps = Booking::calculateTps(
            unitPrice:       (int)   $validated['unit_price'],
            quantity:        (int)   $validated['quantity'],
            discountPercent: (float) ($validated['discount_percent'] ?? 0),
            paidAmount:      (int)   ($validated['paid_amount'] ?? 0),
        );

        $booking->update([
            'service_type_id'  => $validated['service_type_id'],
            'client_name'      => $validated['client_name'],
            'client_contact'   => $validated['client_contact']   ?? null,
            'client_address'   => $validated['client_address']   ?? null,
            'booking_date'     => $validated['booking_date']     ?? null,
            'start_date'       => $validated['start_date']       ?? null,
            'end_date'         => $validated['end_date']         ?? null,
            'booking_time'     => $validated['booking_time']     ?? null,
            'status'           => $validated['status'],
            'quantity'         => $validated['quantity'],
            'unit_price'       => $validated['unit_price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'paid_amount'      => $validated['paid_amount']      ?? 0,
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