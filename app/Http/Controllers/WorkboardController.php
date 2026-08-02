<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class WorkboardController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'semua');

        // Base query: Hanya tampilkan booking yang pembayarannya Lunas atau DP
        $baseQuery = Booking::with('serviceType')
            ->whereIn('payment_status', ['Lunas', 'Down Payment']);

        // Hitung jumlah data per status untuk mengisi angka di kartu atas
        $counts = [
            'semua'            => (clone $baseQuery)->count(),
            'belum_upload'     => (clone $baseQuery)->where('status', 'Dijadwalkan')->count(),
            'belum_pilih_foto' => (clone $baseQuery)->where('status', 'File Original Disiapkan')->count(),
            // Menggabungkan status 'Pilih Foto' dan 'Pilihan Diterima' ke tab Seleksi Masuk
            'seleksi_masuk'    => (clone $baseQuery)->whereIn('status', ['Pilih Foto', 'Pilihan Diterima'])->count(),
            'sedang_diedit'    => (clone $baseQuery)->where('status', 'Proses Edit')->count(),
            'terkirim'         => (clone $baseQuery)->where('status', 'Selesai')->count(),
        ];

        // Filter data untuk Grid berdasarkan tab yang diklik
        $query = clone $baseQuery;
        if ($tab === 'belum_upload') {
            $query->where('status', 'Dijadwalkan');
        } elseif ($tab === 'belum_pilih_foto') {
            $query->where('status', 'File Original Disiapkan');
        } elseif ($tab === 'seleksi_masuk') {
            $query->whereIn('status', ['Pilih Foto', 'Pilihan Diterima']);
        } elseif ($tab === 'sedang_diedit') {
            $query->where('status', 'Proses Edit');
        } elseif ($tab === 'terkirim') {
            $query->where('status', 'Selesai');
        }

        // Ambil data dengan Pagination (12 per halaman agar grid rapi)
        $bookings = $query->orderBy('booking_date', 'asc')
            ->orderBy('booking_time', 'asc')
            ->paginate(12);

        // Pertahankan query string 'tab' saat pindah halaman paginasi
        $bookings->appends(['tab' => $tab]);

        return view('workboard.index', compact('bookings', 'tab', 'counts'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|string',
            'link_hasil' => 'nullable|url',
            'link_folder_kerja' => 'nullable|url',
            'link_original' => 'nullable|url',
            'deadline_pilih' => 'nullable|date',
        ]);

        $dataToUpdate = [
            'status' => $request->status,
        ];

        if ($request->has('link_hasil')) {
            $dataToUpdate['link_hasil'] = $request->link_hasil;
        }
        if ($request->has('link_folder_kerja')) {
            $dataToUpdate['link_folder_kerja'] = $request->link_folder_kerja;
        }
        if ($request->has('link_original')) {
            $dataToUpdate['link_original'] = $request->link_original;
        }
        if ($request->has('deadline_pilih')) {
            $dataToUpdate['deadline_pilih'] = $request->deadline_pilih;
        }

        $booking->update($dataToUpdate);

        return back()->with('success', 'Pengerjaan berhasil diperbarui!');
    }
}