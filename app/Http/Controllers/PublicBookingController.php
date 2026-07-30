<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CompanySetting;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicBookingController extends Controller
{
    public function show(string $ownerId)
    {
        $owner = User::findOrFail($ownerId);
        // PASTIKAN MELOAD RELASI CATEGORY
        $services = ServiceType::with('category')->where('user_id', $owner->id)->orderBy('name')->get();
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
            ->unique()->values()->toArray();

        return view('booking.public', compact('owner', 'services', 'companySetting', 'bookedDates', 'ownerId'));
    }

    public function store(Request $request, string $ownerId)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_contact' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_instagram' => 'nullable|string|max:255',
            'client_address' => 'nullable|string',
            'link_gmaps' => 'nullable|url|max:1000', // <-- TAMBAHAN UNTUK GMAPS
            'service_type_id' => 'required|exists:service_types,id',
            'booking_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'booking_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
            'payment_type' => 'required|string|in:DP,LUNAS',
        ]);

        $owner = User::findOrFail($ownerId);
        $service = ServiceType::where('id', $validated['service_type_id'])
            ->where('user_id', $owner->id)
            ->firstOrFail();

        $tpsData = Booking::calculateTps((int) $service->price, 0, 0);

        $booking = Booking::create([
            'user_id' => $owner->id,
            'service_type_id' => $service->id,
            'client_name' => $validated['client_name'],
            'client_contact' => $validated['client_contact'],
            'client_email' => $validated['client_email'] ?? null,
            'client_instagram' => $validated['client_instagram'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
            'link_gmaps' => $validated['link_gmaps'] ?? null, // <-- MASUKKAN KE DATABASE
            'booking_date' => $validated['booking_date'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'booking_time' => $validated['booking_time'] ?? null,
            'notes' => $validated['notes'] ?? null,

            'status' => 'Pending Bayar',
            'payment_status' => 'Pending',
            'payment_type' => $validated['payment_type'],

            'unit_price' => $tpsData['subtotal'],
            'discount_percent' => 0,
            'paid_amount' => 0,
            'subtotal' => $tpsData['subtotal'],
            'discount_amount' => $tpsData['discount_amount'],
            'total' => $tpsData['total'],
            'remaining' => $tpsData['remaining'],
        ]);

        // Generate Booking Code & Transaksi Pertama
        $bookingCodeFormatted = 'BKG-' . Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));

        $amountExpected = ($validated['payment_type'] === 'LUNAS') ? $tpsData['total'] : (int) ceil($tpsData['total'] * 0.3);

        // Buat Baris Transaksi di Tabel payment_transactions
        \App\Models\PaymentTransaction::create([
            'booking_id' => $booking->id,
            'user_id' => $owner->id,
            'transaction_id' => $bookingCodeFormatted . '-' . $validated['payment_type'] . '-' . time(),
            'payment_type' => $validated['payment_type'] === 'LUNAS' ? 'LUNAS' : 'DP',
            'amount' => $amountExpected,
            'payment_status' => 'Pending',
        ]);

        if (!empty($validated['client_email'])) {
            try {
                $companySetting = CompanySetting::where('user_id', $owner->id)->first();
                $companyName = $companySetting->company_name ?? $owner->name;
                $companyPhone = $companySetting->company_phone ?? null;
                $booking->load('serviceType');

                \Illuminate\Support\Facades\Mail::to($validated['client_email'])->send(
                    new \App\Mail\CustomerBookingReceived($booking, $bookingCodeFormatted, $companyName, $companyPhone)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal kirim email Customer: " . $e->getMessage());
            }
        }

        return redirect()->route('booking.public.pembayaran', ['ownerId' => $ownerId, 'bookingId' => $booking->id])
            ->with('success', 'Booking berhasil! Silakan selesaikan pembayaran.');
    }

    public function checkPage()
    {
        $owner = User::first();
        $companySetting = CompanySetting::where('user_id', $owner->id)->first();

        return view('booking.check-booking', [
            'ownerId' => $owner->id,
            'businessName' => $companySetting->company_name ?? $owner->name,
        ]);
    }

    public function serviceDetail(string $ownerId, string $serviceId)
    {
        $owner = User::findOrFail($ownerId);
        $companySetting = CompanySetting::where('user_id', $owner->id)->first();
        // PASTIKAN MELOAD RELASI CATEGORY DAN GALLERIES-NYA
        $service = ServiceType::with('category.galleries')
            ->where('id', $serviceId)
            ->where('user_id', $owner->id)
            ->firstOrFail();

        return view('booking.service-detail', compact('service', 'owner', 'companySetting', 'ownerId'));
    }

    public function allServices(string $ownerId)
    {
        $owner = User::findOrFail($ownerId);
        // PASTIKAN MELOAD RELASI CATEGORY
        $services = ServiceType::with('category')->where('user_id', $owner->id)->orderBy('name')->get();
        $companySetting = CompanySetting::where('user_id', $owner->id)->first();

        return view('booking.all-services', compact('owner', 'services', 'companySetting', 'ownerId'));
    }

    public function serviceGallery(string $ownerId, string $serviceId)
    {
        $owner = User::findOrFail($ownerId);
        $companySetting = CompanySetting::where('user_id', $owner->id)->first();
        // PASTIKAN MELOAD RELASI CATEGORY DAN GALLERIES-NYA
        $service = ServiceType::with('category.galleries')
            ->where('id', $serviceId)
            ->where('user_id', $owner->id)
            ->firstOrFail();

        return view('booking.service-gallery', compact('service', 'owner', 'companySetting', 'ownerId'));
    }

    public function checkResult(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string',
            'email' => 'required|email'
        ]);

        $inputCode = strtoupper(trim($request->booking_code));
        $inputEmail = strtolower(trim($request->email));

        // Mencari booking berdasarkan email
        $bookings = Booking::where('client_email', $inputEmail)->with('serviceType')->get();
        $matchedBooking = null;

        foreach ($bookings as $b) {
            // Kita membangun ulang kode dari masing-masing booking milik email tersebut
            $expectedCode = 'BKG-' . \Carbon\Carbon::parse($b->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($b->id), 0, 4));

            // Mencocokkan kode yang diketik customer dengan yang di database
            if ($expectedCode === $inputCode) {
                $matchedBooking = $b;
                break;
            }
        }

        if (!$matchedBooking) {
            return back()->with('error', 'Kode Booking atau Email tidak ditemukan. Pastikan data yang kamu ketik sudah benar.');
        }

        $booking = $matchedBooking;
        $ownerId = $booking->user_id;
        $owner = User::findOrFail($ownerId);
        $companySetting = CompanySetting::where('user_id', $ownerId)->first();
        $bookingCode = $inputCode;

        // Logika Nominal
        $isLunas = strtoupper($booking->payment_type) === 'LUNAS' || strtoupper($booking->payment_type) === 'PELUNASAN';
        $amountToPay = 0;
        $dpAmount = (int) ceil($booking->total * 0.3);

        if (in_array($booking->payment_status, ['Pending', 'Belum Bayar', 'Tunggu Konfirmasi'])) {
            $amountToPay = $isLunas ? $booking->total : $dpAmount;
        } elseif ($booking->payment_status === 'Down Payment') {
            $amountToPay = $booking->remaining;
        }

        // Memanggil View track-result.blade.php
        return view('booking.track-result', compact(
            'booking',
            'owner',
            'companySetting',
            'bookingCode',
            'amountToPay',
            'ownerId',
            'isLunas'
        ));
    }

    public function bookingForm(string $ownerId, string $serviceId = null)
    {
        $owner = User::findOrFail($ownerId);
        $companySetting = CompanySetting::where('user_id', $owner->id)->first();
        // PASTIKAN MELOAD RELASI CATEGORY
        $services = ServiceType::with('category')->where('user_id', $owner->id)->orderBy('name')->get();

        $selectedService = null;
        if ($serviceId) {
            $selectedService = $services->firstWhere('id', $serviceId);
        }

        return view('booking.form', compact('owner', 'companySetting', 'services', 'ownerId', 'selectedService'));
    }

    public function paymentSuccess($ownerId, $bookingId)
    {
        $owner = User::findOrFail($ownerId);
        $booking = Booking::where('id', $bookingId)->where('user_id', $owner->id)->with('serviceType')->firstOrFail();
        $companySetting = CompanySetting::where('user_id', $owner->id)->first();

        $total = (int) $booking->total;
        $dpPercent = 30;
        $dpAmount = (int) ceil($total * $dpPercent / 100);
        $isLunas = strtoupper($booking->payment_type) === 'LUNAS' || strtoupper($booking->payment_type) === 'PELUNASAN';

        $amountToPay = 0;
        if (in_array($booking->payment_status, ['Pending', 'Belum Bayar', 'Tunggu Konfirmasi'])) {
            $amountToPay = $isLunas ? $total : $dpAmount;
        } elseif ($booking->payment_status === 'Down Payment') {
            $amountToPay = $booking->remaining;
        }

        $bookingCode = 'BKG-' . \Carbon\Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));

        return view('booking.payment-success', compact(
            'booking',
            'owner',
            'companySetting',
            'bookingCode',
            'amountToPay',
            'ownerId'
        ));
    }

    public function uploadProof(Request $request, string $ownerId, string $bookingId)
    {
        $request->validate([
            'client_email' => 'required|email|max:255',
            'client_instagram' => 'nullable|string|max:255',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $booking = Booking::where('id', $bookingId)->where('user_id', $ownerId)->with('serviceType')->firstOrFail();

        if ($booking->client_email && strtolower(trim($request->client_email)) !== strtolower(trim($booking->client_email))) {
            return back()->withErrors(['client_email' => 'Email verifikasi tidak sesuai dengan data email saat booking.']);
        }

        if ($booking->payment_proof && Storage::disk('public')->exists($booking->payment_proof)) {
            Storage::disk('public')->delete($booking->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $updateData = [
            'payment_proof' => $path,
            'payment_status' => 'Tunggu Konfirmasi',
            'client_email' => $request->client_email,
            'client_instagram' => $request->client_instagram,
        ];

        $isPelunasanProcess = false;
        if ($booking->payment_status === 'Down Payment') {
            $updateData['payment_type'] = 'PELUNASAN';
            $isPelunasanProcess = true;

            $bookingCodeFormatted = 'BKG-' . Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));
            \App\Models\PaymentTransaction::create([
                'booking_id' => $booking->id,
                'user_id' => $ownerId,
                'transaction_id' => $bookingCodeFormatted . '-PELUNASAN-' . time(),
                'payment_type' => 'PELUNASAN',
                'amount' => $booking->remaining > 0 ? $booking->remaining : ($booking->total - ceil($booking->total * 0.3)),
                'payment_status' => 'Tunggu Konfirmasi',
                'payment_proof' => $path,
            ]);
        } else {
            $tx = \App\Models\PaymentTransaction::where('booking_id', $booking->id)->latest()->first();
            if ($tx) {
                $tx->update([
                    'payment_status' => 'Tunggu Konfirmasi',
                    'payment_proof' => $path,
                ]);
            }
        }

        $booking->update($updateData);

        try {
            $owner = User::findOrFail($ownerId);
            $total = (int) $booking->total;
            $dpAmount = (int) ceil($total * 0.3);

            if ($isPelunasanProcess || strtoupper($booking->payment_type) === 'PELUNASAN') {
                $amountToPay = $booking->remaining > 0 ? $booking->remaining : ($total - $dpAmount);
            } elseif (strtoupper($booking->payment_type) === 'LUNAS') {
                $amountToPay = $total;
            } else {
                $amountToPay = $dpAmount;
            }

            $bookingCode = 'BKG-' . Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));

            \Illuminate\Support\Facades\Mail::to($owner->email)->send(
                new \App\Mail\PaymentProofSubmitted($booking, $bookingCode, $amountToPay)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal kirim email upload bukti transfer: " . $e->getMessage());
        }

        return redirect()->route('booking.public.payment-success', ['ownerId' => $ownerId, 'bookingId' => $booking->id]);
    }

    public function pembayaran(string $ownerId, string $bookingId)
    {
        $owner = User::findOrFail($ownerId);
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $owner->id)
            ->with('serviceType')
            ->firstOrFail();
        $companySetting = CompanySetting::where('user_id', $owner->id)->first();

        $subtotal = (int) $booking->subtotal;
        $discountAmount = (int) $booking->discount_amount;
        $discountPercent = (float) $booking->discount_percent;
        $total = (int) $booking->total;

        if ($total === 0 && (int) $booking->unit_price > 0) {
            $tps = Booking::calculateTps((int) $booking->unit_price, $discountPercent, (int) $booking->paid_amount);
            $subtotal = $tps['subtotal'];
            $discountAmount = $tps['discount_amount'];
            $total = $tps['total'];

            $booking->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'remaining' => $tps['remaining'],
                'payment_status' => $tps['payment_status'],
            ]);
        }

        $dpPercent = 30;
        $dpAmount = (int) ceil($total * $dpPercent / 100);
        $sisaAfterDp = $total - $dpAmount;
        $paymentType = strtoupper($booking->payment_type);

        $amountToPay = 0;
        if (in_array($booking->payment_status, ['Pending', 'Belum Bayar', 'Tunggu Konfirmasi'])) {
            $amountToPay = ($paymentType === 'LUNAS' || $paymentType === 'PELUNASAN') ? $total : $dpAmount;
        } elseif ($booking->payment_status === 'Down Payment') {
            $amountToPay = $booking->remaining;
        }

        $bookingCode = 'BKG-' . \Carbon\Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));

        return view('booking.pembayaran', compact(
            'booking',
            'owner',
            'companySetting',
            'subtotal',
            'total',
            'discountAmount',
            'discountPercent',
            'dpPercent',
            'dpAmount',
            'amountToPay',
            'sisaAfterDp',
            'bookingCode',
            'ownerId'
        ));
    }
}