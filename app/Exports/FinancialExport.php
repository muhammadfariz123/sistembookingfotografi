<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\AdditionalIncome;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FinancialExport implements FromView, ShouldAutoSize
{
    protected $month;
    protected $year;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month;
        $this->year = $year ?: now()->year;
    }

    public function view(): View
    {
        $userId = Auth::id();

        // Siapkan Query
        $bookingQuery = Booking::where('user_id', $userId)->with('serviceType');
        $incomeQuery = AdditionalIncome::where('user_id', $userId);
        $expenseQuery = Expense::where('user_id', $userId);

        // Filter berdasarkan Bulan jika dipilih
        if ($this->month) {
            $bookingQuery->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('booking_date')
                        ->whereMonth('booking_date', $this->month)
                        ->whereYear('booking_date', $this->year);
                })->orWhere(function ($q2) {
                    $q2->whereNotNull('start_date')
                        ->whereMonth('start_date', $this->month)
                        ->whereYear('start_date', $this->year);
                });
            });
            $incomeQuery->whereMonth('date', $this->month)->whereYear('date', $this->year);
            $expenseQuery->whereMonth('date', $this->month)->whereYear('date', $this->year);
        }

        // Ambil Data
        $bookings = $bookingQuery->latest('created_at')->get();
        $incomes = $incomeQuery->latest('date')->get();
        $expenses = $expenseQuery->latest('date')->get();
        
        // Format Label Periode
        $periode = $this->month
            ? Carbon::create()->month((int) $this->month)->locale('id')->monthName . ' ' . $this->year
            : 'Semua Waktu';

        // Rekapitulasi Total
        $totalRevenue = $bookings->sum('total');
        $sudahDiterima = $bookings->sum('paid_amount');
        $totalPemasukan = $incomes->sum('amount');
        $totalPengeluaran = $expenses->sum('amount');
        $labaBersih = $sudahDiterima + $totalPemasukan - $totalPengeluaran;

        return view('exports.financial', compact(
            'bookings',
            'incomes',
            'expenses',
            'periode',
            'totalRevenue',
            'sudahDiterima',
            'totalPemasukan',
            'totalPengeluaran',
            'labaBersih'
        ));
    }
}