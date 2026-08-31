<table>
    <tr>
        <th colspan="8" style="font-size: 16px; font-weight: bold; text-align: center;">LAPORAN KEUANGAN</th>
    </tr>
    <tr>
        <th colspan="8" style="text-align: center;">Periode: {{ $periode }}</th>
    </tr>
    <tr></tr>

    {{-- REKAPITULASI --}}
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #f3f4f6;">RINGKASAN</th>
        <th style="font-weight: bold; background-color: #f3f4f6;">TOTAL (Rp)</th>
    </tr>
    <tr>
        <td colspan="2">Total Revenue Booking</td>
        <td>{{ $totalRevenue }}</td>
    </tr>
    <tr>
        <td colspan="2">Pembayaran Booking Diterima</td>
        <td>{{ $sudahDiterima }}</td>
    </tr>
    <tr>
        <td colspan="2">Pemasukan Tambahan</td>
        <td>{{ $totalPemasukan }}</td>
    </tr>
    <tr>
        <td colspan="2">Pengeluaran</td>
        <td>{{ $totalPengeluaran }}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: bold;">Laba Bersih</td>
        <td style="font-weight: bold; {{ $labaBersih < 0 ? 'color: red;' : 'color: green;' }}">
            {{ $labaBersih }}
        </td>
    </tr>
    <tr></tr>
    <tr></tr>

    {{-- TABEL PENGHASILAN DARI BOOKING --}}
    <tr>
        <th colspan="8" style="font-weight: bold; background-color: #dbeafe;">A. RINCIAN PENDAPATAN BOOKING</th>
    </tr>
    <tr>
        <th style="font-weight: bold; border: 1px solid #000;">Tanggal</th>
        <th style="font-weight: bold; border: 1px solid #000;">Nama Klien</th>
        <th style="font-weight: bold; border: 1px solid #000;">Layanan</th>
        <th style="font-weight: bold; border: 1px solid #000;">Status Booking</th>
        <th style="font-weight: bold; border: 1px solid #000;">Status Pembayaran</th>
        <th style="font-weight: bold; border: 1px solid #000;">Total Tagihan (Rp)</th>
        <th style="font-weight: bold; border: 1px solid #000;">Sudah Dibayar (Rp)</th>
        <th style="font-weight: bold; border: 1px solid #000;">Sisa Tagihan (Rp)</th>
    </tr>
    @foreach($bookings as $booking)
        <tr>
            <td style="border: 1px solid #000;">
                {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') : \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
            </td>
            <td style="border: 1px solid #000;">{{ $booking->client_name }}</td>
            <td style="border: 1px solid #000;">{{ $booking->serviceType->name ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $booking->status }}</td>
            <td style="border: 1px solid #000;">{{ $booking->payment_status }}</td>
            <td style="border: 1px solid #000;">{{ $booking->total }}</td>
            <td style="border: 1px solid #000;">{{ $booking->paid_amount }}</td>
            <td style="border: 1px solid #000;">{{ $booking->remaining }}</td>
        </tr>
    @endforeach
    @if($bookings->isEmpty())
        <tr><td colspan="8" style="border: 1px solid #000; text-align: center;">Tidak ada data booking di periode ini</td></tr>
    @endif
    <tr></tr>

    {{-- TABEL PEMASUKAN TAMBAHAN --}}
    <tr>
        <th colspan="4" style="font-weight: bold; background-color: #dbeafe;">B. RINCIAN PEMASUKAN TAMBAHAN</th>
    </tr>
    <tr>
        <th style="font-weight: bold; border: 1px solid #000;">Tanggal</th>
        <th colspan="2" style="font-weight: bold; border: 1px solid #000;">Deskripsi</th>
        <th style="font-weight: bold; border: 1px solid #000;">Nominal (Rp)</th>
    </tr>
    @foreach($incomes as $income)
        <tr>
            <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($income->date)->format('d/m/Y') }}</td>
            <td colspan="2" style="border: 1px solid #000;">{{ $income->description }}</td>
            <td style="border: 1px solid #000;">{{ $income->amount }}</td>
        </tr>
    @endforeach
    @if($incomes->isEmpty())
        <tr><td colspan="4" style="border: 1px solid #000; text-align: center;">Tidak ada pemasukan tambahan di periode ini</td></tr>
    @endif
    <tr></tr>

    {{-- TABEL PENGELUARAN --}}
    <tr>
        <th colspan="4" style="font-weight: bold; background-color: #fee2e2;">C. RINCIAN PENGELUARAN</th>
    </tr>
    <tr>
        <th style="font-weight: bold; border: 1px solid #000;">Tanggal</th>
        <th colspan="2" style="font-weight: bold; border: 1px solid #000;">Deskripsi</th>
        <th style="font-weight: bold; border: 1px solid #000;">Nominal (Rp)</th>
    </tr>
    @foreach($expenses as $expense)
        <tr>
            <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
            <td colspan="2" style="border: 1px solid #000;">{{ $expense->description }}</td>
            <td style="border: 1px solid #000;">{{ $expense->amount }}</td>
        </tr>
    @endforeach
    @if($expenses->isEmpty())
        <tr><td colspan="4" style="border: 1px solid #000; text-align: center;">Tidak ada pengeluaran di periode ini</td></tr>
    @endif
</table>