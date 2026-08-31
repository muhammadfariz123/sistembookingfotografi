<?php

namespace App\Http\Controllers;

use App\Exports\FinancialExport;
use App\Models\AdditionalIncome;
use App\Models\Booking;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class FinancialController extends Controller
{
    /**
     * Menampilkan Dashboard Keuangan dan memproses Filter Bulan
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // ── 1. Handle Export Excel ───────────────────────────────
        if ($request->has('export')) {
            $month = $request->month;
            $year  = $request->get('year', now()->year);

            $filename = 'Laporan-Keuangan-' . ($month ? $month . '-' . $year : 'Semua-Waktu') . '.xlsx';
            return Excel::download(new FinancialExport($month, $year), $filename);
        }

        // ── 2. Persiapan Query Dasar (Hanya milik User yang Login) ──
        $bookingQuery = Booking::where('user_id', $userId);
        $incomeQuery  = AdditionalIncome::where('user_id', $userId);
        $expenseQuery = Expense::where('user_id', $userId);

        // ── 3. Terapkan Filter Bulan (Jika Ada) ─────────────────
        if ($request->filled('month')) {
            $month = (int) $request->month;
            $year  = (int) $request->get('year', now()->year);
            
            $bookingQuery->where(function ($q) use ($month, $year) {
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

        // ── 4. Ambil Data dari Database ──────────────────────────
        $bookings          = $bookingQuery->latest('created_at')->get();
        $additionalIncomes = $incomeQuery->latest('date')->get();
        $expenses          = $expenseQuery->latest('date')->get();

        // ── 5. Perhitungan Rumus Keuangan (Sesuai Proposal Bab 3) ──
        
        // Rumus 3.6 — Revenue = Σ Total_i
        $revenue = $bookings->sum('total');

        // Rumus 3.7 — Sudah Diterima = Σ Db_i
        $sudahDiterima = $bookings->sum('paid_amount');

        // Rumus 3.8 — Belum Dibayar = Σ Total_i (Hanya yang berstatus 'Pending')
        $belumDibayar = $bookings->where('payment_status', 'Pending')->sum('total');

        // Rumus 3.9 — Sisa Tagihan = Σ Sisa_i (Pending + DP)
        $sisaTagihan = $bookings->whereIn('payment_status', ['Pending', 'Down Payment'])->sum('remaining');

        // Rumus 3.10 — Laba Bersih = Sudah Diterima + Tambahan - Pengeluaran
        $totalPemasukan   = $additionalIncomes->sum('amount');
        $totalPengeluaran = $expenses->sum('amount');
        $labaBersih       = $sudahDiterima + $totalPemasukan - $totalPengeluaran;

        // ── 6. Perhitungan Tren Chart & Status ────────────────────
        
        // Menghitung status pembayaran untuk kartu ringkasan
        $statusCount = [
            'lunas'       => $bookings->where('payment_status', 'Lunas')->count(),
            'dp'          => $bookings->where('payment_status', 'Down Payment')->count(),
            'belum_bayar' => $bookings->where('payment_status', 'Pending')->count(),
        ];

        // Tren revenue per bulan (Selalu mengambil SEMUA data agar grafik tidak kosong saat difilter 1 bulan)
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

        // ── 7. Kembalikan ke View ──────────────────────────────────
        return view('financial.index', compact(
            'revenue',
            'sudahDiterima',
            'belumDibayar',
            'sisaTagihan',
            'totalPemasukan',
            'totalPengeluaran',
            'labaBersih',
            'bookings',
            'additionalIncomes',
            'expenses',
            'trendData',
            'statusCount'
        ));
    }

    /**
     * Menyimpan data Pemasukan Tambahan via AJAX
     */
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

        return response()->json(['success' => true, 'message' => 'Pemasukan tambahan berhasil disimpan.']);
    }

    /**
     * Menyimpan data Pengeluaran via AJAX
     */
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

        return response()->json(['success' => true, 'message' => 'Pengeluaran berhasil disimpan.']);
    }

    /**
     * Menghapus data Pemasukan Tambahan via AJAX
     */
    public function destroyIncome(AdditionalIncome $income)
    {
        if ($income->user_id !== Auth::id()) abort(403);
        
        $income->delete();
        return response()->json(['success' => true, 'message' => 'Pemasukan dihapus.']);
    }

    /**
     * Menghapus data Pengeluaran via AJAX
     */
    public function destroyExpense(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) abort(403);
        
        $expense->delete();
        return response()->json(['success' => true, 'message' => 'Pengeluaran dihapus.']);
    }
}