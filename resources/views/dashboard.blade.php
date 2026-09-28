{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">
        
        @if($showOnboarding)
        @php
            $completedCount = collect($checklist)->filter()->count();
            
            // Cari langkah pertama yang belum selesai
            $nextStep = null;
            $stepsData = [
                'settings' => ['title' => '1. Pengaturan Toko', 'desc' => 'Nama studio, kontak, & QRIS', 'route' => route('company-setting.edit')],
                'categories' => ['title' => '2. Kategori & Portofolio', 'desc' => 'Buat kategori & upload foto', 'route' => route('service-categories.index')],
                'services' => ['title' => '3. Layanan & Paket', 'desc' => 'Tentukan harga & detail paket', 'route' => route('service-types.index')],
                'bookings' => ['title' => '4. Pantau Daftar Booking', 'desc' => 'Pantau pesanan masuk & status sesi', 'route' => route('bookings.listPage')],
                'transactions' => ['title' => '5. Daftar Transaksi', 'desc' => 'Konfirmasi bukti bayar klien', 'route' => route('transactions.index')],
                'calendar' => ['title' => '6. Kalender Jadwal', 'desc' => 'Pantau jadwal sesi foto', 'route' => route('bookings.calendar')],
                'workboard' => ['title' => '7. Papan Kerja', 'desc' => 'Kirim folder kerja & link hasil', 'route' => route('workboard.index')],
                'financial' => ['title' => '8. Laporan Keuangan', 'desc' => 'Pantau pendapatan & pengeluaran', 'route' => route('financial.index')],
            ];

            foreach ($checklist as $key => $isCompleted) {
                if (!$isCompleted && isset($stepsData[$key])) {
                    $nextStep = $stepsData[$key];
                    break;
                }
            }
        @endphp
        
        @if($nextStep)
        <div x-data="{ dismissed: localStorage.getItem('onboarding_dismissed') === 'true' }" 
             x-show="!dismissed"
             class="bg-white border border-gray-200 rounded-[20px] shadow-sm mb-7 overflow-hidden transition-all duration-300">
            
            <div class="w-full text-left p-4 md:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3 md:gap-4">
                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i data-lucide="compass" class="w-5 h-5 md:w-6 md:h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-[15px] md:text-lg font-bold text-gray-900 leading-tight mb-0.5 md:mb-1">Panduan: {{ $nextStep['title'] }}</h2>
                        <div class="flex items-center gap-2 text-[12px] md:text-sm text-gray-500 font-medium">
                            <span class="text-blue-600 font-bold">{{ $completedCount }} dari 8 Selesai</span>
                            <span class="hidden sm:inline">&bull;</span>
                            <span class="hidden sm:inline">{{ $nextStep['desc'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                    <!-- Tombol Lanjut -->
                    <a href="{{ $nextStep['route'] }}" class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full text-[13px] font-bold text-gray-700 transition-colors">
                        Lanjut <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                    <!-- Tombol Dismiss -->
                    <button @click.stop="dismissed = true; localStorage.setItem('onboarding_dismissed', 'true')" class="flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 rounded-full text-[13px] font-bold text-red-600 transition-colors" title="Sembunyikan Panduan">
                        <i data-lucide="x" class="w-4 h-4"></i> <span class="hidden sm:inline">Sembunyikan</span>
                    </button>
                </div>
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