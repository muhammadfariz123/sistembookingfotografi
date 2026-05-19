<?php
// app/Http/Controllers/BookingExportController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingExportController extends Controller
{
    public function export(Request $request)
    {
        $userId = Auth::id();

        // ── Ambil semua booking milik user dengan filter opsional ─
        $query = Booking::where('user_id', $userId)->with('serviceType');

        if ($request->filled('month') && !$request->filled('date_from')) {
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
        }

        if ($request->filled('date_from')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('booking_date', '>=', $request->date_from)
                  ->orWhereDate('start_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('booking_date', '<=', $request->date_to)
                  ->orWhereDate('start_date', '<=', $request->date_to);
            });
        }

        $bookings = $query->latest('created_at')->get();

        // ── Generate nama file ─────────────────────────────────────
        $filename = 'booking-data-' . now()->format('Y-m-d') . '.xlsx';

        // ── Stream file langsung ke browser ───────────────────────
        return response()->streamDownload(function () use ($bookings) {
            $this->generateExcel($bookings);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function generateExcel($bookings): void
    {
        // Gunakan PHP built-in untuk generate xlsx sederhana
        // via format CSV yang dibungkus dengan proper headers
        // Karena tidak ada library di production, kita gunakan
        // pendekatan native PHP dengan output buffer

        $spreadsheet = $this->buildSpreadsheet($bookings);
        echo $spreadsheet;
    }

    private function formatRp(int|float|null $value): string
    {
        if ($value === null || $value == 0) return 'Rp 0';
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    private function formatTanggal($dateStr): string
    {
        if (!$dateStr) return '-';
        try {
            return Carbon::parse($dateStr)->locale('id')->isoFormat('dddd, D MMMM YYYY');
        } catch (\Exception $e) {
            return '-';
        }
    }

    private function formatDateTime($dateStr): string
    {
        if (!$dateStr) return '-';
        try {
            return Carbon::parse($dateStr)->locale('id')->isoFormat('D MMMM YYYY, HH.mm');
        } catch (\Exception $e) {
            return '-';
        }
    }

    private function buildSpreadsheet($bookings): string
    {
        // Gunakan openpyxl via Python untuk generate file xlsx yang rapi
        // Karena Laravel production tidak selalu punya PhpSpreadsheet,
        // kita generate menggunakan Python subprocess yang sudah tersedia

        $data = [];
        foreach ($bookings as $booking) {
            $tgl = $booking->booking_date ?? $booking->start_date;
            $data[] = [
                'client_name'     => $booking->client_name ?? '-',
                'client_contact'  => $booking->client_contact ?? '-',
                'client_address'  => $booking->client_address ?? '-',
                'service_name'    => $booking->serviceType?->name ?? '-',
                'quantity'        => $booking->quantity ?? 1,
                'unit_price'      => $this->formatRp($booking->unit_price),
                'subtotal'        => $this->formatRp(($booking->unit_price ?? 0) * ($booking->quantity ?? 1)),
                'booking_date'    => $this->formatTanggal($tgl),
                'booking_time'    => $booking->booking_time
                    ? Carbon::parse($booking->booking_time)->format('H:i')
                    : '-',
                'status'          => $booking->status ?? '-',
                'payment_status'  => $booking->payment_status ?? '-',
                'payment_method'  => '-',
                'additional_fee'  => '-',
                'total_additional'=> '-',
                'total'           => $this->formatRp($booking->total),
                'paid_amount'     => $this->formatRp($booking->paid_amount),
                'remaining'       => $this->formatRp($booking->remaining),
                'notes'           => $booking->notes ?? '-',
                'created_at'      => $this->formatDateTime($booking->created_at),
                'updated_at'      => $this->formatDateTime($booking->updated_at),
            ];
        }

        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        $tmpJson  = tempnam(sys_get_temp_dir(), 'booking_') . '.json';
        $tmpXlsx  = tempnam(sys_get_temp_dir(), 'booking_') . '.xlsx';

        file_put_contents($tmpJson, $jsonData);

        $script = $this->getPythonScript($tmpJson, $tmpXlsx);
        $tmpPy   = tempnam(sys_get_temp_dir(), 'export_') . '.py';
        file_put_contents($tmpPy, $script);

        exec("python3 {$tmpPy} 2>&1", $output, $code);

        $content = '';
        if ($code === 0 && file_exists($tmpXlsx)) {
            $content = file_get_contents($tmpXlsx);
        }

        // Cleanup
        @unlink($tmpJson);
        @unlink($tmpXlsx);
        @unlink($tmpPy);

        return $content;
    }

    private function getPythonScript(string $jsonFile, string $outFile): string
    {
        return <<<PYTHON
import json, openpyxl
from openpyxl.styles import Font, PatternFill, Alignment, Border, Side
from openpyxl.utils import get_column_letter

with open('{$jsonFile}', 'r', encoding='utf-8') as f:
    rows = json.load(f)

wb = openpyxl.Workbook()
ws = wb.active
ws.title = 'Data Booking'

headers = [
    'Nama Klien', 'Kontak Klien', 'Alamat Klien',
    'Layanan 1', 'Quantity 1', 'Harga Layanan 1', 'Subtotal 1',
    'Tanggal Booking', 'Waktu Booking', 'Status', 'Status Pembayaran',
    'Metode Pembayaran', 'Biaya Tambahan', 'Total Biaya Tambahan',
    'Total Keseluruhan', 'Sudah Dibayar', 'Sisa Pembayaran',
    'Catatan', 'Dibuat Pada', 'Diupdate Pada'
]

header_fill   = PatternFill('solid', start_color='2563EB', end_color='2563EB')
header_font   = Font(bold=True, color='FFFFFF', name='Arial', size=10)
header_align  = Alignment(horizontal='center', vertical='center', wrap_text=True)
thin          = Side(border_style='thin', color='D1D5DB')
border        = Border(left=thin, right=thin, top=thin, bottom=thin)

for col, h in enumerate(headers, 1):
    cell = ws.cell(row=1, column=col, value=h)
    cell.fill    = header_fill
    cell.font    = header_font
    cell.alignment = header_align
    cell.border  = border

ws.row_dimensions[1].height = 32

keys = [
    'client_name','client_contact','client_address',
    'service_name','quantity','unit_price','subtotal',
    'booking_date','booking_time','status','payment_status',
    'payment_method','additional_fee','total_additional',
    'total','paid_amount','remaining',
    'notes','created_at','updated_at'
]

alt_fill  = PatternFill('solid', start_color='F0F9FF', end_color='F0F9FF')
data_font = Font(name='Arial', size=10)
data_align = Alignment(vertical='center', wrap_text=False)

for r_idx, row in enumerate(rows, 2):
    is_alt = (r_idx % 2 == 0)
    for c_idx, key in enumerate(keys, 1):
        val  = row.get(key, '-')
        cell = ws.cell(row=r_idx, column=c_idx, value=val)
        cell.font      = data_font
        cell.alignment = data_align
        cell.border    = border
        if is_alt:
            cell.fill = alt_fill
    ws.row_dimensions[r_idx].height = 18

col_widths = [22,18,22,18,10,15,15,24,12,14,16,16,14,16,16,14,14,22,20,20]
for i, w in enumerate(col_widths, 1):
    ws.column_dimensions[get_column_letter(i)].width = w

ws.freeze_panes = 'A2'

ws.auto_filter.ref = ws.dimensions

wb.save('{$outFile}')
PYTHON;
    }
}