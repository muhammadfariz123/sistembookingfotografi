<?php
// app/Http/Controllers/BookingController.php
namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmedToCustomer;
use App\Mail\AdminPaymentApprovedNotification; 
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingExport;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function getBookingData()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['serviceType', 'transactions']) 
            ->latest()
            ->get();

        $now = Carbon::now('Asia/Jakarta');
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $todayStr = $now->format('Y-m-d');
        $lastMonthDate = $now->copy()->subMonth();
        $lastMonth = $lastMonthDate->month;
        $lastYear = $lastMonthDate->year;

        $bookingThisMonthCount = 0;
        $revenueThisMonth = 0;
        $bookingLastMonthCount = 0;
        $revenueLastMonth = 0;
        $pendingCount = 0;
        $pendingValue = 0;
        $todaySessionCount = 0;
        $allTodaySessionsCount = 0;
        $todaySchedules = [];

        $mappedBookings = $bookings->map(function ($booking) use (
            $currentMonth, $currentYear, $lastMonth, $lastYear, $todayStr,
            &$bookingThisMonthCount, &$revenueThisMonth, &$bookingLastMonthCount, &$revenueLastMonth,
            &$pendingCount, &$pendingValue, &$todaySessionCount, &$allTodaySessionsCount, &$todaySchedules
        ) {
            $created_at = $booking->created_at;
            $updated_at = $booking->updated_at;
            $paid_at = $booking->paid_at;
            
            $bookingCode = 'BKG-' . ($created_at ? $created_at->format('Ymd') : date('Ymd')) . '-' . strtoupper(substr(md5($booking->id), 0, 4));

            $createdAtWib = $created_at ? $created_at->copy()->timezone('Asia/Jakarta')->toIso8601String() : null;
            $updatedAtWib = $updated_at ? $updated_at->copy()->timezone('Asia/Jakarta')->toIso8601String() : null;
            $paidAtWib = $paid_at ? $paid_at->copy()->timezone('Asia/Jakarta')->toIso8601String() : null;

            $mappedTransactions = $booking->transactions->map(function ($tx) use ($bookingCode, $booking) {
                return [
                    'id' => $tx->id,
                    'transaction_id' => $tx->transaction_id,
                    'booking_code' => $bookingCode,
                    'client_name' => $booking->client_name,
                    'client_contact' => $booking->client_contact,
                    'client_email' => $booking->client_email,
                    'client_address' => $booking->client_address,
                    'service_type' => $booking->serviceType,
                    'payment_type' => $tx->payment_type, 
                    'amount' => (int) $tx->amount,
                    'payment_status' => $tx->payment_status, 
                    'payment_proof' => $tx->payment_proof ?? $booking->payment_proof,
                    'admin_notes' => $tx->admin_notes ?? $booking->notes,
                    'paid_at' => $tx->paid_at ? $tx->paid_at->copy()->timezone('Asia/Jakarta')->toIso8601String() : ($tx->payment_status === 'Berhasil' && $tx->updated_at ? $tx->updated_at->copy()->timezone('Asia/Jakarta')->toIso8601String() : null),
                    'created_at' => $tx->created_at ? $tx->created_at->copy()->timezone('Asia/Jakarta')->toIso8601String() : null,
                ];
            });

            $tglLayananStr = $booking->booking_date ? $booking->booking_date->format('Y-m-d') : ($booking->start_date ? $booking->start_date->format('Y-m-d') : null);

            $item = [
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
                    'duration' => $booking->serviceType->duration, 
                ] : null,
                'unit_price' => (int) $booking->unit_price,
                'total' => (int) $booking->total,
                'paid_amount' => (int) $booking->paid_amount,
                'remaining' => (int) $booking->remaining,
                'paid_at' => $paidAtWib,
                'payment_status' => $booking->payment_status,
                'payment_type' => $booking->payment_type,
                'payment_proof' => $booking->payment_proof,
                'status' => $booking->status,
                'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
                'start_date' => $booking->start_date ? $booking->start_date->format('Y-m-d') : null,
                'end_date' => $booking->end_date ? $booking->end_date->format('Y-m-d') : null,
                'booking_time' => $booking->booking_time,
                'notes' => $booking->notes,
                'link_folder_kerja' => $booking->link_folder_kerja,
                'link_original' => $booking->link_original,
                'link_hasil' => $booking->link_hasil,
                'deadline_pilih' => $booking->deadline_pilih,
                'queue_number' => $booking->queue_number,
                'transactions' => $mappedTransactions, 
                'created_at' => $createdAtWib,
                'updated_at' => $updatedAtWib,
            ];

            // Hitung statistik dalam satu perulangan yang sama
            if ($created_at) {
                // Hati-hati zona waktu saat menghitung bulan
                $localDate = $created_at->copy()->timezone('Asia/Jakarta');
                if ($localDate->month == $currentMonth && $localDate->year == $currentYear) {
                    $bookingThisMonthCount++;
                    $revenueThisMonth += $booking->paid_amount;
                } elseif ($localDate->month == $lastMonth && $localDate->year == $lastYear) {
                    $bookingLastMonthCount++;
                    $revenueLastMonth += $booking->paid_amount;
                }
            }

            if (in_array($booking->payment_status, ['Pending', 'Belum Bayar', 'Tunggu Konfirmasi'])) {
                $pendingCount++;
                $pendingValue += $booking->remaining;
            }

            if ($tglLayananStr === $todayStr) {
                $allTodaySessionsCount++;
                if ($booking->status === 'Dijadwalkan') {
                    $todaySessionCount++;
                    $todaySchedules[] = $item;
                }
            }

            return $item;
        });

        $bookingGrowth = $bookingLastMonthCount > 0 ? (($bookingThisMonthCount - $bookingLastMonthCount) / $bookingLastMonthCount) * 100 : ($bookingThisMonthCount > 0 ? 100 : 0);
        $revenueGrowth = $revenueLastMonth > 0 ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100 : ($revenueThisMonth > 0 ? 100 : 0);
        $todayConversionRate = $allTodaySessionsCount > 0 ? round(($todaySessionCount / $allTodaySessionsCount) * 100) : 0;

        // Urutkan jadwal hari ini berdasarkan waktu
        usort($todaySchedules, function($a, $b) {
            return strcmp($a['booking_time'] ?? '99:99', $b['booking_time'] ?? '99:99');
        });

        $summary = [
            'current_month_name' => $now->locale('id')->isoFormat('MMM'),
            'today_date_name' => $now->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'booking_this_month' => $bookingThisMonthCount,
            'booking_growth' => round($bookingGrowth),
            'revenue_this_month' => $revenueThisMonth,
            'revenue_growth' => round($revenueGrowth),
            'pending_count' => $pendingCount,
            'pending_value' => $pendingValue,
            'today_session_count' => $todaySessionCount,
            'today_session_confirmed' => $todaySessionCount,
            'today_conversion_rate' => $todayConversionRate,
            'today_schedules' => $todaySchedules,
        ];

        return [
            'data' => $mappedBookings,
            'summary' => $summary,
        ];
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json($this->getBookingData());
        }
        return view('bookings.index');
    }

    public function listPage()
    {
        $initialData = $this->getBookingData();
        return view('bookings.list', ['initialBookings' => $initialData['data']]);
    }

    public function calendarPage()
    {
        $initialData = $this->getBookingData();
        return view('bookings.calendar', ['initialBookings' => $initialData['data']]);
    }

    public function edit(Booking $booking)
    {
        if ($booking->user_id !== Auth::id())
            abort(403);
        $serviceTypes = ServiceType::where('user_id', Auth::id())->get();
        return view('bookings.form', compact('booking', 'serviceTypes'));
    }

    public function approvePayment(Request $request, $id)
    {
        // Cari apakah $id merujuk ke Booking atau PaymentTransaction
        $booking = Booking::where('id', $id)->first();
        if (!$booking) {
            $tx = \App\Models\PaymentTransaction::find($id);
            if ($tx) {
                $booking = Booking::find($tx->booking_id);
            }
        }

        if (!$booking || $booking->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Data booking tidak ditemukan.'], 404);
        }

        // Cari transaksi yang statusnya masih 'Tunggu Konfirmasi' untuk mengambil nominalnya
        $pendingTx = \App\Models\PaymentTransaction::where('booking_id', $booking->id)
            ->where('payment_status', 'Tunggu Konfirmasi')
            ->first();

        $type = strtoupper($booking->payment_type);
        $dpAmount = (int) ceil($booking->total * 0.3);

        // Ambil nominal langsung dari tabel riwayat transaksi, jika gagal otomatis hitung matematis
        $currentPaymentAmount = $pendingTx ? $pendingTx->amount : ($type === 'DP' ? $dpAmount : max($booking->total - $booking->paid_amount, 0));

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
            'link_hasil',
            'link_folder_kerja',
            'link_original',
            'deadline_pilih',
            'paid_amount' => $totalPaid,
            'remaining' => max($booking->total - $totalPaid, 0),
            'paid_at' => Carbon::now('Asia/Jakarta'),
        ]);

        // Update juga status di payment_transactions menjadi Berhasil
        \App\Models\PaymentTransaction::where('booking_id', $booking->id)
            ->where('payment_status', 'Tunggu Konfirmasi')
            ->update(['payment_status' => 'Berhasil', 'paid_at' => Carbon::now('Asia/Jakarta')]);

        // ==============================================================
        // LOGIKA BARU: KIRIM EMAIL NOTIFIKASI
        // ==============================================================
        try {
            // Generate ulang Booking Code persis seperti yang di UI
            $bookingCode = 'BKG-' . Carbon::parse($booking->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($booking->id), 0, 4));

            // Ambil data pemilik/studio (admin saat ini)
            $owner = \App\Models\User::find($booking->user_id);
            $companySetting = DB::table('company_settings')->where('user_id', $booking->user_id)->first();

            $companyName = $companySetting->company_name ?? $owner->name;
            $companyPhone = $companySetting->whatsapp_number ?? $companySetting->phone_number ?? '';

            // 1. Kirim Email ke Customer (Jika email diisi)
            if (!empty($booking->client_email)) {
                Mail::to($booking->client_email)->send(new PaymentConfirmedToCustomer(
                    $booking,
                    $bookingCode,
                    $companyName,
                    $companyPhone,
                    $currentPaymentAmount
                ));
            }

            // 2. Kirim Email Notifikasi ke Admin (Pemilik Studio)
            if (!empty($owner->email)) {
                Mail::to($owner->email)->send(new AdminPaymentApprovedNotification(
                    $booking,
                    $bookingCode,
                    $currentPaymentAmount
                ));
            }

        } catch (\Exception $e) {
            // Jangan menggagalkan request jika email gagal, cukup catat di file .log
            Log::error('Gagal mengirim email notifikasi: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function rejectPayment(Request $request, $id)
    {
        $booking = Booking::where('id', $id)->first();
        if (!$booking) {
            $tx = \App\Models\PaymentTransaction::find($id);
            if ($tx) {
                $booking = Booking::find($tx->booking_id);
            }
        }

        if (!$booking || $booking->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Data booking tidak ditemukan.'], 404);
        }

        $reason = $request->reason ?? 'Bukti transfer tidak valid/nominal tidak sesuai.';
        $booking->update([
            'payment_status' => 'Ditolak',
            'status' => 'Dibatalkan',
            'notes' => $booking->notes . "\n\n[DITOLAK]: " . $reason
        ]);

        \App\Models\PaymentTransaction::where('booking_id', $booking->id)
            ->where('payment_status', 'Tunggu Konfirmasi')
            ->update(['payment_status' => 'Ditolak']);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran ditolak dan booking dibatalkan.'
        ]);
    }

    public function updateNotes(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id())
            abort(403);
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
            paidAmount: (int) ($validated['paid_amount'] ?? 0),
        );

        $booking = Booking::create(array_merge($validated, [
            'user_id' => Auth::id(),
            'subtotal' => $tps['subtotal'],
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
        if ($booking->user_id !== Auth::id())
            abort(403);
        
        $validated = $request->validated();
        ServiceType::where('id', $validated['service_type_id'])->where('user_id', Auth::id())->firstOrFail();
        
        $tps = Booking::calculateTps(
            unitPrice: (int) $validated['unit_price'],
            paidAmount: (int) ($validated['paid_amount'] ?? 0),
        );

        $booking->update(array_merge($validated, [
            'subtotal' => $tps['subtotal'],
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
        session(['onboarding_excel_downloaded' => true]);
        if (ob_get_contents()) ob_end_clean();
        return Excel::download(new BookingExport($request->all()), 'Data_Booking.xlsx');
    }
}