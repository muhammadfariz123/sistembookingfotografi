{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">
        
        @if($showOnboarding)
        @php
            $completedCount = collect($checklist)->filter()->count();
            
            $stepsData = [
                ['title' => '1. Pengaturan Toko', 'desc' => 'Nama studio, kontak, & QRIS', 'route' => route('company-setting.edit'), 'completed' => $checklist['settings'] ?? false],
                ['title' => '2. Kategori & Portofolio', 'desc' => 'Buat kategori & upload foto', 'route' => route('service-categories.index'), 'completed' => $checklist['categories'] ?? false],
                ['title' => '3. Layanan & Paket', 'desc' => 'Tentukan harga & detail paket', 'route' => route('service-types.index'), 'completed' => $checklist['services'] ?? false],
                ['title' => '4. Pantau Daftar Booking', 'desc' => 'Pantau pesanan masuk & status sesi', 'route' => route('bookings.listPage'), 'completed' => $checklist['bookings'] ?? false],
                ['title' => '5. Daftar Transaksi', 'desc' => 'Konfirmasi bukti bayar klien', 'route' => route('transactions.index'), 'completed' => $checklist['transactions'] ?? false],
                ['title' => '6. Kalender Jadwal', 'desc' => 'Pantau jadwal sesi foto', 'route' => route('bookings.calendar'), 'completed' => $checklist['calendar'] ?? false],
                ['title' => '7. Papan Kerja', 'desc' => 'Kirim folder kerja & link hasil', 'route' => route('workboard.index'), 'completed' => $checklist['workboard'] ?? false],
                ['title' => '8. Laporan Keuangan', 'desc' => 'Pantau pendapatan & pengeluaran', 'route' => route('financial.index'), 'completed' => $checklist['financial'] ?? false],
            ];

            // Cari langkah pertama yang belum selesai
            $nextStep = null;
            foreach ($stepsData as $step) {
                if (!$step['completed']) {
                    $nextStep = $step;
                    break;
                }
            }
        @endphp
        
        @if($nextStep)
        <div x-data="{ dismissed: localStorage.getItem('onboarding_dismissed') === 'true' }" 
             x-show="!dismissed"
             class="bg-white border border-gray-200 rounded-[20px] shadow-sm mb-7 p-4 md:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all duration-300">
            
            <div class="flex items-center gap-3 md:gap-4 flex-1">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="compass" class="w-5 h-5 md:w-6 md:h-6"></i>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-0.5 md:mb-1">
                        <span class="text-[10px] md:text-[11px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full whitespace-nowrap">{{ $completedCount }}/8 Selesai</span>
                        <h2 class="text-[14px] md:text-base font-bold text-gray-900 leading-tight">Selanjutnya: {{ $nextStep['title'] }}</h2>
                    </div>
                    <p class="text-[12px] md:text-[13px] text-gray-500 font-medium">{{ $nextStep['desc'] }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 self-end md:self-auto">
                <a href="{{ $nextStep['route'] }}" class="flex items-center gap-1.5 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-[12px] md:text-[13px] font-bold transition-colors shadow-sm">
                    Lanjutkan <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
                <button @click.stop="dismissed = true; localStorage.setItem('onboarding_dismissed', 'true')" class="flex items-center gap-1.5 px-4 py-2.5 bg-red-50 hover:bg-red-100 rounded-full text-[12px] md:text-[13px] font-bold text-red-600 transition-colors" title="Sembunyikan Panduan">
                    <i data-lucide="x" class="w-4 h-4"></i> <span class="hidden sm:inline">Sembunyikan</span>
                </button>
            </div>
        </div>
        @endif
        @endif

        {{-- HEADER SUMMARY & TINDAKAN CEPAT --}}
        <x-dashboard.dashboard-header :initialSummary="$initialSummary ?? null" />

        {{-- Jika Ingin Ditambahkan Fitur Tambahan (Misal Recent Booking) di Masa Depan, Letakkan Di Bawah Sini --}}
        
    </div>

    <script>
        document.addEventListener('turbo:load', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>
</x-app-layout>