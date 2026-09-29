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
            $firstUncompletedIndex = 0;
            foreach ($stepsData as $index => $step) {
                if (!$step['completed']) {
                    $firstUncompletedIndex = $index;
                    break;
                }
            }
        @endphp
        
        <div x-data="{ 
                steps: {{ json_encode($stepsData) }},
                currentIndex: {{ $firstUncompletedIndex }},
                dismissed: localStorage.getItem('onboarding_dismissed') === 'true'
             }" 
             x-show="!dismissed"
             class="bg-white border border-gray-200 rounded-[20px] shadow-sm mb-7 p-4 md:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all duration-300">
            
            <div class="flex items-center gap-3 md:gap-4 flex-1">
                <!-- Ikon Kiri -->
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="compass" class="w-5 h-5 md:w-6 md:h-6"></i>
                </div>
                
                <!-- Konten Tutorial (Bisa diklik) -->
                <a :href="steps[currentIndex].route" class="flex-1 group block hover:bg-gray-50 p-2 -ml-2 rounded-xl transition-colors cursor-pointer" title="Klik untuk membuka halaman ini">
                    <div class="flex flex-wrap items-center gap-2 mb-0.5 md:mb-1">
                        <!-- Status Badge -->
                        <span x-show="steps[currentIndex].completed" class="text-[10px] md:text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1" style="display: none;">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg> Selesai
                        </span>
                        <span x-show="!steps[currentIndex].completed" class="text-[10px] md:text-[11px] font-bold text-orange-700 bg-orange-100 px-2 py-0.5 rounded-full" style="display: none;">
                            Belum
                        </span>
                        
                        <h2 class="text-[14px] md:text-base font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition-colors" x-text="steps[currentIndex].title"></h2>
                    </div>
                    <div class="flex items-center gap-2 text-[12px] md:text-[13px] text-gray-500 font-medium">
                        <span x-text="steps[currentIndex].desc"></span>
                        <span class="text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                            &bull; Buka Halaman
                        </span>
                    </div>
                </a>
            </div>

            <!-- Kontrol Navigasi Kanan -->
            <div class="flex items-center gap-3 shrink-0 self-end md:self-auto">
                
                <!-- Prev & Next Control -->
                <div class="flex items-center gap-1 bg-gray-50 rounded-full p-1 border border-gray-200 shadow-sm">
                    <button @click="currentIndex = (currentIndex > 0) ? currentIndex - 1 : steps.length - 1" class="p-1.5 hover:bg-white hover:shadow-sm rounded-full text-gray-500 hover:text-blue-600 transition-all" title="Sebelumnya">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <span class="text-[11px] font-bold text-gray-500 w-8 text-center" x-text="(currentIndex + 1) + '/' + steps.length"></span>
                    <button @click="currentIndex = (currentIndex < steps.length - 1) ? currentIndex + 1 : 0" class="p-1.5 hover:bg-white hover:shadow-sm rounded-full text-gray-500 hover:text-blue-600 transition-all" title="Selanjutnya">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                
                <!-- Tombol Dismiss -->
                <button @click.stop="dismissed = true; localStorage.setItem('onboarding_dismissed', 'true')" class="flex items-center gap-1.5 px-3 md:px-4 py-2 md:py-2.5 bg-red-50 hover:bg-red-100 rounded-full text-[12px] md:text-[13px] font-bold text-red-600 transition-colors shadow-sm" title="Sembunyikan Panduan">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg> 
                    <span class="hidden sm:inline">Tutup</span>
                </button>
            </div>
        </div>
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