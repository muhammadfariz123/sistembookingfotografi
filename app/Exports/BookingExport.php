<?php

namespace App\Exports;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class BookingExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithColumnWidths
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Data Booking';
    }

    public function collection()
    {
        $query = Booking::where('user_id', Auth::id())
            ->with('serviceType')
            ->orderBy('created_at', 'desc');

        if (!empty($this->filters['month']) && empty($this->filters['date_from'])) {
            $month = (int) $this->filters['month'];
            $year  = (int) ($this->filters['year'] ?? now()->year);
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
        }

        if (!empty($this->filters['date_from'])) {
            $dateFrom = Carbon::parse($this->filters['date_from'])->startOfDay();
            $query->where(function ($q) use ($dateFrom) {
                $q->where('booking_date', '>=', $dateFrom)
                  ->orWhere('start_date', '>=', $dateFrom);
            });
        }

        if (!empty($this->filters['date_to'])) {
            $dateTo = Carbon::parse($this->filters['date_to'])->endOfDay();
            $query->where(function ($q) use ($dateTo) {
                $q->where('booking_date', '<=', $dateTo)
                  ->orWhere('start_date', '<=', $dateTo);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Klien',
            'Kontak Klien',
            'Alamat Klien',
            'Nama Layanan',
            'Harga Paket',
            'Subtotal',
            'Tanggal Booking',
            'Waktu Booking',
            'Status',
            'Status Pembayaran',
            'Total Keseluruhan',
            'Sudah Dibayar',
            'Sisa Pembayaran',
            'Catatan',
            'Dibuat Pada',
            'Diupdate Pada',
        ];
    }

    public function map($booking): array
    {
        $tanggal = '-';
        if ($booking->booking_date) {
            $tanggal = Carbon::parse($booking->booking_date)
                ->locale('id')->isoFormat('dddd, D MMMM YYYY');
        } elseif ($booking->start_date) {
            $start   = Carbon::parse($booking->start_date)->locale('id')->isoFormat('D MMMM YYYY');
            $end     = $booking->end_date
                ? Carbon::parse($booking->end_date)->locale('id')->isoFormat('D MMMM YYYY')
                : '';
            $tanggal = $end ? "{$start} – {$end}" : $start;
        }

        $waktu    = $booking->booking_time ? substr($booking->booking_time, 0, 5) : '-';
        
        // Subtotal sekarang murni mengambil dari unit_price, tanpa perkalian quantity
        $subtotal = $booking->unit_price ?? 0;

        return [
            $booking->client_name         ?? '-',
            $booking->client_contact      ?? '-',
            $booking->client_address      ?? '-',
            $booking->serviceType?->name  ?? '-',
            'Rp ' . number_format($booking->unit_price  ?? 0, 0, ',', '.'),
            'Rp ' . number_format($subtotal,               0, ',', '.'),
            $tanggal,
            $waktu,
            $booking->status              ?? '-',
            $booking->payment_status      ?? '-',
            'Rp ' . number_format($booking->total        ?? 0, 0, ',', '.'),
            'Rp ' . number_format($booking->paid_amount  ?? 0, 0, ',', '.'),
            'Rp ' . number_format($booking->remaining    ?? 0, 0, ',', '.'),
            $booking->notes               ?? '-',
            Carbon::parse($booking->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH.mm'),
            Carbon::parse($booking->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH.mm'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,  // Nama Klien
            'B' => 20,  // Kontak Klien
            'C' => 25,  // Alamat Klien
            'D' => 20,  // Jenis Layanan
            'E' => 18,  // Harga Paket
            'F' => 18,  // Subtotal
            'G' => 30,  // Tanggal Booking
            'H' => 12,  // Waktu Booking
            'I' => 14,  // Status
            'J' => 18,  // Status Pembayaran
            'K' => 20,  // Total Keseluruhan
            'L' => 18,  // Sudah Dibayar
            'M' => 18,  // Sisa Pembayaran
            'N' => 30,  // Catatan
            'O' => 22,  // Dibuat Pada
            'P' => 22,  // Diupdate Pada
        ];
    }
}