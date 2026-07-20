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
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $bookings = Booking::where('user_id', Auth::id())
                ->with('serviceType')
                ->latest()
                ->get();

            $mappedBookings = $bookings->map(function ($booking) {
                $bookingCode = 'BKG-' . Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));

                return [
                    'id' => $booking->id,
                    'booking_code' => $bookingCode,
                    'client_name' => $booking->client_name,
                    'client_contact' => $booking->client_contact,
                    'client_email' => $booking->client_email,
                    'client_address' => $booking->client_address,
                    'service_type' => $booking->serviceType ? [
                        'id' => $booking->serviceType->id,
                        'name' => $booking->serviceType->name,
                        'price' => $booking->serviceType->price,
                    ] : null,
                    'unit_price' => (int) $booking->unit_price,
                    'total' => (int) $booking->total,
                    'paid_amount' => (int) $booking->paid_amount,
                    'remaining' => (int) $booking->remaining,
                    'paid_at' => $booking->paid_at?->format('Y-m-d H:i:s'),
                    'payment_status' => $booking->payment_status,
                    'payment_type' => $booking->payment_type,
                    'payment_proof' => $booking->payment_proof,
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

            $now = Carbon::now('Asia/Jakarta');
            $currentMonth = $now->month;
            $currentYear = $now->year;
            $todayStr = $now->format('Y-m-d');

            $lastMonthDate = $now->copy()->subMonth();
            $lastMonth = $lastMonthDate->month;
            $lastYear = $lastMonthDate->year;

            $bookingsThisMonth = collect($mappedBookings)->filter(function ($b) use ($currentMonth, $currentYear) {
                $date = Carbon::parse($b['created_at'])->timezone('Asia/Jakarta');
                return $date->month == $currentMonth && $date->year == $currentYear;
            });
            $bookingThisMonthCount = $bookingsThisMonth->count();
            $revenueThisMonth = $bookingsThisMonth->sum('paid_amount');

            $bookingsLastMonth = collect($mappedBookings)->filter(function ($b) use ($lastMonth, $lastYear) {
                $date = Carbon::parse($b['created_at'])->timezone('Asia/Jakarta');
                return $date->month == $lastMonth && $date->year == $lastYear;
            });
            $bookingLastMonthCount = $bookingsLastMonth->count();
            $revenueLastMonth = $bookingsLastMonth->sum('paid_amount');

            $bookingGrowth = $bookingLastMonthCount > 0 ? (($bookingThisMonthCount - $bookingLastMonthCount) / $bookingLastMonthCount) * 100 : ($bookingThisMonthCount > 0 ? 100 : 0);
            $revenueGrowth = $revenueLastMonth > 0 ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100 : ($revenueThisMonth > 0 ? 100 : 0);

            $pendingBookings = collect($mappedBookings)->filter(function ($b) {
                return in_array($b['payment_status'], ['Pending', 'Belum Bayar', 'Tunggu Konfirmasi']);
            });

            $todaySessions = collect($mappedBookings)->filter(function ($b) use ($todayStr) {
                $tglLayanan = $b['booking_date'] ?? $b['start_date'];
                return $tglLayanan === $todayStr && $b['status'] === 'Dijadwalkan';
            });

            $allTodaySessions = collect($mappedBookings)->filter(function ($b) use ($todayStr) {
                $tglLayanan = $b['booking_date'] ?? $b['start_date'];
                return $tglLayanan === $todayStr;
            });
            $todayConversionRate = $allTodaySessions->count() > 0 ? round(($todaySessions->count() / $allTodaySessions->count()) * 100) : 0;

            $summary = [
                'current_month_name' => $now->locale('id')->isoFormat('MMM'),
                'today_date_name' => $now->locale('id')->isoFormat('dddd, D MMMM YYYY'),
                'booking_this_month' => $bookingThisMonthCount,
                'booking_growth' => round($bookingGrowth),
                'revenue_this_month' => $revenueThisMonth,
                'revenue_growth' => round($revenueGrowth),
                'pending_count' => $pendingBookings->count(),
                'pending_value' => $pendingBookings->sum('remaining'),
                'today_session_count' => $todaySessions->count(),
                'today_session_confirmed' => $todaySessions->count(),
                'today_conversion_rate' => $todayConversionRate,
                'today_schedules' => $todaySessions->sortBy('booking_time')->values()->toArray(),
            ];

            return response()->json([
                'data' => $mappedBookings,
                'summary' => $summary,
            ]);
        }

        return view('bookings.index');
    }

    public function listPage()
    {
        return view('bookings.list');
    }

    public function calendarPage()
    {
        return view('bookings.calendar');
    }

    // [DIHAPUS]: public function create() sudah dibuang karena beralih ke form publik.

    public function edit(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);
        $serviceTypes = ServiceType::where('user_id', Auth::id())->get();
        return view('bookings.form', compact('booking', 'serviceTypes'));
    }

    public function approvePayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $type = strtoupper($booking->payment_type);
        $dpAmount = (int) ceil($booking->total * 0.3);

        if ($type === 'PELUNASAN' || $type === 'LUNAS') {
            $totalPaid = $booking->total;
            $paymentStatus = 'Lunas';
            $message = 'Pembayaran berhasil dikonfirmasi dan berstatus Lunas!';
        } else {
            $totalPaid = $dpAmount;
            $paymentStatus = 'Down Payment';
            $message = 'Pembayaran DP 30% berhasil dikonfirmasi!';
        }

        $booking->update([
            'payment_status' => $paymentStatus,
            'status' => 'Dijadwalkan',
            'paid_amount' => $totalPaid,
            'remaining' => max($booking->total - $totalPaid, 0),
            'paid_at' => Carbon::now('Asia/Jakarta'),
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function rejectPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $reason = $request->reason ?? 'Bukti transfer tidak valid/nominal tidak sesuai.';
        $booking->update([
            'payment_status' => 'Ditolak',
            'status' => 'Dibatalkan',
            'notes' => $booking->notes . "\n\n[DITOLAK]: " . $reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran ditolak dan booking dibatalkan.'
        ]);
    }

    public function updateNotes(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $request->validate(['notes' => 'nullable|string|max:2000']);
        $booking->update(['notes' => $request->notes]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan/Notes berhasil diperbarui.'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:bookings,id']);

        Booking::where('user_id', Auth::id())
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' data transaksi berhasil dihapus.'
        ]);
    }

    public function store(BookingRequest $request)
    {
        $validated = $request->validated();
        ServiceType::where('id', $validated['service_type_id'])->where('user_id', Auth::id())->firstOrFail();

        $tps = Booking::calculateTps(
            unitPrice: (int) $validated['unit_price'],
            discountPercent: (float) ($validated['discount_percent'] ?? 0),
            paidAmount: (int) ($validated['paid_amount'] ?? 0),
        );

        $booking = Booking::create(array_merge($validated, [
            'user_id' => Auth::id(),
            'subtotal' => $tps['subtotal'],
            'discount_amount' => $tps['discount_amount'],
            'total' => $tps['total'],
            'remaining' => $tps['remaining'],
            'payment_status' => $tps['payment_status'],
            'paid_at' => $tps['payment_status'] === 'Lunas' ? Carbon::now('Asia/Jakarta') : null,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil disimpan.',
            'data' => $booking->load('serviceType'),
        ], 201);
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);

        $validated = $request->validated();
        ServiceType::where('id', $validated['service_type_id'])->where('user_id', Auth::id())->firstOrFail();

        $tps = Booking::calculateTps(
            unitPrice: (int) $validated['unit_price'],
            discountPercent: (float) ($validated['discount_percent'] ?? 0),
            paidAmount: (int) ($validated['paid_amount'] ?? 0),
        );

        $booking->update(array_merge($validated, [
            'subtotal' => $tps['subtotal'],
            'discount_amount' => $tps['discount_amount'],
            'total' => $tps['total'],
            'remaining' => $tps['remaining'],
            'payment_status' => $tps['payment_status'],
            'paid_at' => $tps['payment_status'] === 'Lunas' && !$booking->paid_at ? Carbon::now('Asia/Jakarta') : $booking->paid_at,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil diperbarui.',
            'data' => $booking->fresh()->load('serviceType'),
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

    public function export(Request $request)
    {
        return Excel::download(new BookingExport($request->all()), 'Data_Booking.xlsx');
    }
}