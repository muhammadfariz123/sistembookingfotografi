<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CompanySetting;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    public function show(string $ownerId)
    {
        $owner = User::findOrFail($ownerId);

        $services = ServiceType::where('user_id', $owner->id)
            ->orderBy('name')
            ->get();

        $companySetting = CompanySetting::where('user_id', $owner->id)->first();

        $bookedDates = Booking::where('user_id', $owner->id)
            ->whereIn('status', ['Dijadwalkan'])
            ->get()
            ->flatMap(function ($booking) {
                if ($booking->booking_date) {
                    return [Carbon::parse($booking->booking_date)->format('Y-m-d')];
                }
                if ($booking->start_date && $booking->end_date) {
                    $dates = [];
                    $start = Carbon::parse($booking->start_date);
                    $end = Carbon::parse($booking->end_date);
                    while ($start->lte($end)) {
                        $dates[] = $start->format('Y-m-d');
                        $start->addDay();
                    }
                    return $dates;
                }
                return [];
            })
            ->unique()
            ->values()
            ->toArray();

        return view('booking.public', compact(
            'owner',
            'services',
            'companySetting',
            'bookedDates',
            'ownerId'
        ));
    }

    public function store(string $ownerId, Request $request)
    {
        $owner = User::findOrFail($ownerId);

        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_contact' => 'required|string|max:255',
            'client_address' => 'nullable|string|max:500',
            'service_type_id' => 'required|exists:service_types,id',
            'quantity' => 'required|integer|min:1',
            'booking_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'booking_time' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $service = ServiceType::where('id', $request->service_type_id)
            ->where('user_id', $owner->id)
            ->firstOrFail();

        $quantity = (int) $request->quantity;
        $unitPrice = (int) $service->price;
        $discountPercent = 0;

        // ── Hitung manual sesuai rumus TPS ────────────────────
        // Rumus 3.1: Subtotal = P × Q
        $subtotal = $unitPrice * $quantity;
        // Rumus 3.2: Nd (diskon) = 0 karena dari klien tidak ada diskon
        $discountAmount = 0;
        // Rumus 3.3: Total = Subtotal - Nd
        $total = $subtotal - $discountAmount;
        // Rumus 3.4: paid_amount = 0 (belum bayar saat submit)
        $paidAmount = 0;
        // Rumus 3.5: Sisa = Total - Db
        $remaining = $total - $paidAmount;

        $booking = Booking::create([
            'user_id' => $owner->id,
            'client_name' => $request->client_name,
            'client_contact' => $request->client_contact,
            'client_address' => $request->client_address ?? '-',
            'service_type_id' => $service->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPercent,
            'paid_amount' => $paidAmount,
            'booking_date' => $request->booking_date ?: null,
            'start_date' => $request->start_date ?: null,
            'end_date' => $request->end_date ?: null,
            'booking_time' => $request->booking_time ?: null,
            'status' => 'Dijadwalkan',
            'notes' => $request->notes ?? null,
        ]);

        // ── Redirect ke invoice menggunakan booking->id (integer PK) ──
        return redirect()->route('booking.public.invoice', [
            'ownerId' => $ownerId,
            'bookingId' => $booking->id,
        ]);
    }

    public function invoice(string $ownerId, string $bookingId)
    {
        $owner = User::findOrFail($ownerId);

        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $owner->id)
            ->with('serviceType')
            ->firstOrFail();

        $companySetting = CompanySetting::where('user_id', $owner->id)->first();

        // ── Ambil nilai langsung dari DB (sudah disimpan via calculateTps) ──
        $subtotal = (int) $booking->subtotal;
        $discountAmount = (int) $booking->discount_amount;
        $discountPercent = (float) $booking->discount_percent;
        $total = (int) $booking->total;

        // ── Fallback: kalau booking lama yang total-nya 0, hitung ulang ──
        if ($total === 0 && $booking->unit_price > 0) {
            $tps = \App\Models\Booking::calculateTps(
                (int) $booking->unit_price,
                (int) $booking->quantity,
                $discountPercent,
                (int) $booking->paid_amount
            );
            $subtotal = $tps['subtotal'];
            $discountAmount = $tps['discount_amount'];
            $total = $tps['total'];
        }

        // DP minimal 30%
        $dpPercent = 30;
        $dpAmount = (int) ceil($total * $dpPercent / 100);
        $sisaAfterDp = $total - $dpAmount;

        $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));

        return view('booking.invoice', compact(
            'booking',
            'owner',
            'companySetting',
            'subtotal',
            'total',
            'discountAmount',
            'discountPercent',
            'dpPercent',
            'dpAmount',
            'sisaAfterDp',
            'invoiceNumber',
            'ownerId'
        ));
    }
}