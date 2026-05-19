<?php

namespace App\Http\Controllers;

use App\Exports\BookingExport;
use App\Models\AdditionalIncome;
use App\Models\Booking;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // ── Handle Export Excel ───────────────────────────────
        if ($request->has('export')) {
            $filters  = $request->only(['month', 'year', 'date_from', 'date_to']);
            $filename = 'booking-data-' . now()->format('Y-m-d') . '.xlsx';
            return Excel::download(new BookingExport($filters), $filename);
        }

        // ── Query dasar ───────────────────────────────────────
        $query        = Booking::where('user_id', $userId)->with('serviceType');
        $incomeQuery  = AdditionalIncome::where('user_id', $userId);
        $expenseQuery = Expense::where('user_id', $userId);

        // ── Filter: prioritas tanggal custom, lalu bulan ──────
        $hasDateRange = $request->filled('date_from') || $request->filled('date_to');
        $hasMonth     = $request->filled('month') && !$hasDateRange;

        if ($hasDateRange) {
            if ($request->filled('date_from')) {
                $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                $query->where(function ($q) use ($dateFrom) {
                    $q->where('booking_date', '>=', $dateFrom)
                      ->orWhere('start_date', '>=', $dateFrom);
                });
                $incomeQuery->whereDate('date', '>=', $request->date_from);
                $expenseQuery->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $dateTo = Carbon::parse($request->date_to)->endOfDay();
                $query->where(function ($q) use ($dateTo) {
                    $q->where('booking_date', '<=', $dateTo)
                      ->orWhere('start_date', '<=', $dateTo);
                });
                $incomeQuery->whereDate('date', '<=', $request->date_to);
                $expenseQuery->whereDate('date', '<=', $request->date_to);
            }
        } elseif ($hasMonth) {
            $month = (int) $request->month;
            $year  = (int) $request->get('year', now()->year);
            $query->where(function ($q) use ($month, $year) {
                $q->where(function ($q2) use ($month, $year) {
                    $q2->whereNotNull('booking_date')
                       ->whereMonth('booking_date', $month)
                       ->whereYear('booking_date', $year);
                })->orWhere(function ($q2) use ($month, $year) {
                    $q2->whereNotNull('start_date')
                       ->whereMonth('start_date', $month)
                       ->whereYear('start_date', $year);
                });
            });
            $incomeQuery->whereMonth('date', $month)->whereYear('date', $year);
            $expenseQuery->whereMonth('date', $month)->whereYear('date', $year);
        }

        $bookings          = $query->latest('created_at')->get();
        $additionalIncomes = $incomeQuery->latest()->get();
        $expenses          = $expenseQuery->latest()->get();

        // ── Rumus 3.6 — Revenue = Σ Total_i ──────────────────
        $revenue = $bookings->sum('total');

        // ── Rumus 3.7 — Sudah Diterima = Σ Db_i ──────────────
        $sudahDiterima = $bookings->sum('paid_amount');

        // ── Rumus 3.8 — Belum Dibayar = Σ Total_i (Belum Bayar)
        $belumDibayar = $bookings
            ->where('payment_status', 'Belum Bayar')
            ->sum('total');

        // ── Rumus 3.9 — Sisa Tagihan = Σ Sisa_i (Belum Bayar + DP)
        $sisaTagihan = $bookings
            ->whereIn('payment_status', ['Belum Bayar', 'Down Payment'])
            ->sum('remaining');

        $totalPemasukan   = $additionalIncomes->sum('amount');
        $totalPengeluaran = $expenses->sum('amount');

        // ── Rumus 3.10 — Laba Bersih ──────────────────────────
        // Laba Bersih = Sudah Diterima + Pemasukan Tambahan – Total Pengeluaran
        $labaBersih = $sudahDiterima + $totalPemasukan - $totalPengeluaran;

        // ── Tren revenue per bulan (selalu dari semua data) ───
        $allBookings = Booking::where('user_id', $userId)->get();
        $trendData   = $allBookings->groupBy(function ($b) {
            $date = $b->booking_date ?? $b->start_date;
            return $date ? Carbon::parse($date)->format('Y-m') : null;
        })
        ->filter(fn($g, $key) => $key !== null)
        ->map(fn($group) => [
            'total' => $group->sum('total'),
            'count' => $group->count(),
        ])
        ->sortKeys();

        // ── Revenue per layanan ────────────────────────────────
        $revenueByService = $bookings
            ->groupBy(fn($b) => $b->serviceType?->name ?? 'Tidak Diketahui')
            ->map(fn($group) => [
                'total'   => $group->sum('total'),
                'count'   => $group->count(),
                'average' => $group->count() > 0
                    ? round($group->sum('total') / $group->count())
                    : 0,
            ])
            ->sortByDesc('total');

        // ── Status pembayaran count ────────────────────────────
        $statusCount = [
            'lunas'       => $bookings->where('payment_status', 'Lunas')->count(),
            'dp'          => $bookings->where('payment_status', 'Down Payment')->count(),
            'belum_bayar' => $bookings->where('payment_status', 'Belum Bayar')->count(),
        ];

        // ── Collection rate ────────────────────────────────────
        $collectionRate = $revenue > 0
            ? round(($sudahDiterima / $revenue) * 100, 1)
            : 0;

        // ── Rata-rata ──────────────────────────────────────────
        $uniqueMonths         = $trendData->count() ?: 1;
        $avgRevenuePerMonth   = round($revenue / $uniqueMonths);
        $avgRevenuePerBooking = $bookings->count() > 0
            ? round($revenue / $bookings->count())
            : 0;

        return view('financial.index', compact(
            'revenue', 'sudahDiterima', 'belumDibayar', 'sisaTagihan',
            'totalPemasukan', 'totalPengeluaran', 'labaBersih',
            'bookings', 'additionalIncomes', 'expenses',
            'trendData', 'revenueByService', 'statusCount',
            'collectionRate', 'avgRevenuePerMonth', 'avgRevenuePerBooking'
        ));
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount'      => 'required|integer|min:1',
            'date'        => 'required|date',
        ]);

        AdditionalIncome::create([
            'user_id'     => Auth::id(),
            'description' => $request->description,
            'amount'      => $request->amount,
            'date'        => $request->date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pemasukan tambahan berhasil disimpan.',
        ]);
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount'      => 'required|integer|min:1',
            'date'        => 'required|date',
        ]);

        Expense::create([
            'user_id'     => Auth::id(),
            'description' => $request->description,
            'amount'      => $request->amount,
            'date'        => $request->date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil disimpan.',
        ]);
    }

    public function destroyIncome(AdditionalIncome $income)
    {
        if ($income->user_id !== Auth::id()) abort(403);
        $income->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemasukan dihapus.',
        ]);
    }

    public function destroyExpense(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) abort(403);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran dihapus.',
        ]);
    }
}