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
            'Email Klien',
            'Instagram Klien',
            'Alamat Klien',
            'Jenis Layanan',
            'Tanggal Booking',
            'Waktu Booking',
            'Status Progres',
            'Status Pembayaran',
            'Tipe Pembayaran',
            'Total Tagihan',
            'Sudah Dibayar',
            'Sisa Pembayaran',
            'No. Antrean',
            'Tautan Google Maps',
            'Tautan Folder Kerja',
            'Tautan Folder Seleksi',
            'Batas Seleksi Foto',
            'Tautan Hasil Foto',
            'Estimasi Selesai',
            'Catatan Pelanggan',
            'Catatan Admin',
            'Dibuat Pada',
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

        $estimasiSelesai = $booking->estimate_date 
            ? Carbon::parse($booking->estimate_date)->locale('id')->isoFormat('D MMMM YYYY')
            : '-';
            
        $deadlinePilih = $booking->deadline_pilih 
            ? Carbon::parse($booking->deadline_pilih)->locale('id')->isoFormat('D MMMM YYYY, HH.mm')
            : '-';

        return [
            $booking->client_name         ?? '-',
            $booking->client_contact      ?? '-',
            $booking->client_email        ?? '-',
            $booking->client_instagram    ?? '-',
            $booking->client_address      ?? '-',
            $booking->serviceType?->name  ?? '-',
            $tanggal,
            $waktu,
            $booking->status              ?? '-',
            $booking->payment_status      ?? '-',
            $booking->payment_type        ?? '-',
            'Rp ' . number_format($booking->total        ?? 0, 0, ',', '.'),
            'Rp ' . number_format($booking->paid_amount  ?? 0, 0, ',', '.'),
            'Rp ' . number_format($booking->remaining    ?? 0, 0, ',', '.'),
            $booking->queue_number        ?? '-',
            $booking->link_gmaps          ?? '-',
            $booking->link_folder_kerja   ?? '-',
            $booking->link_original       ?? '-',
            $deadlinePilih,
            $booking->link_hasil          ?? '-',
            $estimasiSelesai,
            $booking->notes               ?? '-',
            $booking->admin_notes         ?? '-',
            Carbon::parse($booking->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH.mm'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,  // Nama Klien
            'B' => 20,  // Kontak Klien
            'C' => 22,  // Email Klien
            'D' => 20,  // Instagram Klien
            'E' => 25,  // Alamat Klien
            'F' => 20,  // Jenis Layanan
            'G' => 30,  // Tanggal Booking
            'H' => 12,  // Waktu Booking
            'I' => 14,  // Status Progres
            'J' => 18,  // Status Pembayaran
            'K' => 18,  // Tipe Pembayaran
            'L' => 20,  // Total Tagihan
            'M' => 18,  // Sudah Dibayar
            'N' => 18,  // Sisa Pembayaran
            'O' => 14,  // No. Antrean
            'P' => 25,  // Tautan Google Maps
            'Q' => 25,  // Tautan Folder Kerja
            'R' => 25,  // Tautan Folder Seleksi
            'S' => 22,  // Batas Seleksi Foto
            'T' => 25,  // Tautan Hasil Foto
            'U' => 20,  // Estimasi Selesai
            'V' => 30,  // Catatan Pelanggan
            'W' => 30,  // Catatan Admin
            'X' => 22,  // Dibuat Pada
        ];
    }
}