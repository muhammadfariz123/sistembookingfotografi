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
             class="bg-white border border-gray-200 rounded-[20px] shadow-sm mb-7 p-4 md:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 transition-all duration-300">
            
            <div class="flex items-center gap-3 md:gap-4 flex-1">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="compass" class="w-5 h-5 md:w-6 md:h-6"></i>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-0.5 md:mb-1">
                        <h2 class="text-[15px] md:text-lg font-bold text-gray-900 leading-tight" x-text="steps[currentIndex].title"></h2>
                        
                        <span x-show="steps[currentIndex].completed" class="text-[10px] md:text-[11px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full flex items-center gap-1" style="display: none;">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg> Selesai
                        </span>
                        <span x-show="!steps[currentIndex].completed" class="text-[10px] md:text-[11px] font-bold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full" style="display: none;">
                            Belum Selesai
                        </span>
                        
                        <span class="text-[10px] md:text-[11px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full whitespace-nowrap">{{ $completedCount }}/8 Total Selesai</span>
                    </div>
                    <p class="text-[12px] md:text-sm text-gray-500 font-medium" x-text="steps[currentIndex].desc"></p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 shrink-0 w-full lg:w-auto mt-2 lg:mt-0">
                <!-- Prev & Next Control -->
                <div class="flex items-center gap-1 mr-0 md:mr-2 bg-gray-50 rounded-full p-1 border border-gray-100">
                    <button @click="currentIndex = (currentIndex > 0) ? currentIndex - 1 : steps.length - 1" class="p-1.5 hover:bg-white hover:shadow-sm rounded-full text-gray-400 hover:text-blue-600 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <span class="text-[11px] font-bold text-gray-400 w-8 text-center" x-text="(currentIndex + 1) + '/' + steps.length"></span>
                    <button @click="currentIndex = (currentIndex < steps.length - 1) ? currentIndex + 1 : 0" class="p-1.5 hover:bg-white hover:shadow-sm rounded-full text-gray-400 hover:text-blue-600 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <!-- Tombol Action -->
                <a :href="steps[currentIndex].route" class="flex items-center gap-1.5 px-4 md:px-5 py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-[12px] md:text-[13px] font-bold transition-colors shadow-sm">
                    Lanjut <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
                <!-- Tombol Dismiss -->
                <button @click.stop="dismissed = true; localStorage.setItem('onboarding_dismissed', 'true')" class="flex items-center gap-1.5 px-3 md:px-4 py-2 md:py-2.5 bg-red-50 hover:bg-red-100 rounded-full text-[12px] md:text-[13px] font-bold text-red-600 transition-colors" title="Sembunyikan Panduan">
                    <i data-lucide="x" class="w-4 h-4"></i> <span class="hidden sm:inline">Sembunyikan</span>
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