{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">
        
        @if($showOnboarding)
            <div id="onboarding-guide-card" class="bg-white border border-gray-200 rounded-[20px] p-6 shadow-sm mb-7 scroll-mt-6 transition-all duration-300">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i data-lucide="compass" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-grow min-w-0">
                        <h2 class="text-lg font-bold text-gray-900 mb-1">Panduan Memulai Pengoperasian Sistem</h2>
                        <p class="text-sm text-gray-500 mb-4">
                            Selesaikan langkah-langkah di bawah ini untuk mengonfigurasi dan memahami alur kerja sistem booking fotografi Anda secara maksimal.
                        </p>

                        @php
                            $completedCount = collect($checklist)->filter()->count();
                            $percent = ($completedCount / 9) * 100;
                        @endphp

                        <!-- Progress Bar -->
                        <div class="mb-6 bg-gray-50 border border-gray-100 p-4 rounded-xl">
                            <div class="flex items-center justify-between text-xs font-semibold text-gray-600 mb-2">
                                <span>Progress Setup Sistem</span>
                                <span class="text-blue-600">{{ $completedCount }} dari 9 Langkah Selesai ({{ round($percent) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>

                        <!-- Grid Steps -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- Langkah 1 -->
                            <a href="{{ route('company-setting.edit') }}" 
                               class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group
                               {{ $checklist['settings'] 
                                   ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                                   : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800 font-medium' }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                                        {{ $checklist['settings'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                                        @if($checklist['settings'])
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        @else
                                            1
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold truncate">1. Pengaturan Toko</h4>
                                        <p class="text-xs text-gray-400 truncate">Nama studio, kontak, & QRIS</p>
                                    </div>
                                </div>
                                @if(!$checklist['settings'])
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0"></i>
                                @endif
                            </a>

                            <!-- Langkah 2 -->
                            <a href="{{ route('service-categories.index') }}" 
                               class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group
                               {{ $checklist['categories'] 
                                   ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                                   : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                                        {{ $checklist['categories'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                                        @if($checklist['categories'])
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        @else
                                            2
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold truncate">2. Kategori & Portofolio</h4>
                                        <p class="text-xs text-gray-400 truncate">Buat kategori & upload foto</p>
                                    </div>
                                </div>
                                @if(!$checklist['categories'])
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0"></i>
                                @endif
                            </a>

                            <!-- Langkah 3 -->
                            <a href="{{ route('service-types.index') }}" 
                               class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group
                               {{ $checklist['services'] 
                                   ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                                   : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                                        {{ $checklist['services'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                                        @if($checklist['services'])
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        @else
                                            3
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold truncate">3. Layanan & Paket</h4>
                                        <p class="text-xs text-gray-400 truncate">Tentukan harga & detail paket</p>
                                    </div>
                                </div>
                                @if(!$checklist['services'])
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0"></i>
                                @endif
                            </a>

                            <!-- Langkah 4 -->
                            <a href="{{ route('bookings.listPage') }}" 
                             <!-- Langkah 4 -->
                             <a href="{{ route('bookings.listPage') }}" 
                                class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group
                                {{ $checklist['bookings'] 
                                    ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                                    : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                                 <div class="flex items-center gap-3 min-w-0">
                                     <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                                         {{ $checklist['bookings'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                                         @if($checklist['bookings'])
                                             <i data-lucide="check" class="w-4 h-4"></i>
                                         @else
                                             4
                                         @endif
                                     </div>
                                     <div class="min-w-0">
                                         <h4 class="text-sm font-bold truncate">4. Pantau Daftar Booking</h4>
                                         <p class="text-xs text-gray-400 truncate">Pantau pesanan masuk & status sesi</p>
                                     </div>
                                 </div>
                                 @if(!$checklist['bookings'])
                                     <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0"></i>
                                 @endif
                             </a>

                             <!-- Langkah 5 (Pantau Daftar Transaksi) -->
                             <a href="{{ route('transactions.index') }}" 
                                class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group
                                {{ $checklist['transactions'] 
                                    ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                                    : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                                 <div class="flex items-center gap-3 min-w-0">
                                     <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                                         {{ $checklist['transactions'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                                         @if($checklist['transactions'])
                                             <i data-lucide="check" class="w-4 h-4"></i>
                                         @else
                                             5
                                         @endif
                                     </div>
                                     <div class="min-w-0">
                                         <h4 class="text-sm font-bold truncate">5. Pantau Daftar Transaksi</h4>
                                         <p class="text-xs text-gray-400 truncate">Konfirmasi bukti pembayaran klien</p>
                                     </div>
                                 </div>
                                 @if(!$checklist['transactions'])
                                     <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0"></i>
                                 @endif
                             </a>

                             <!-- Langkah 6 (Kalender Jadwal) -->
                             <a href="{{ route('bookings.calendar') }}" 
                                class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group
                                {{ $checklist['calendar'] 
                                    ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                                    : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                                 <div class="flex items-center gap-3 min-w-0">
                                     <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                                         {{ $checklist['calendar'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                                         @if($checklist['calendar'])
                                             <i data-lucide="check" class="w-4 h-4"></i>
                                         @else
                                             6
                                         @endif
                                     </div>
                                     <div class="min-w-0">
                                         <h4 class="text-sm font-bold truncate">6. Kalender Jadwal</h4>
                                         <p class="text-xs text-gray-400 truncate">Pantau jadwal sesi foto mendatang</p>
                                     </div>
                                 </div>
                                 @if(!$checklist['calendar'])
                                     <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0"></i>
                                 @endif
                             </a>

                             <!-- Langkah 7 (Papan Kerja / Workboard) -->
                             <a href="{{ route('workboard.index') }}" 
                                class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group
                                {{ $checklist['workboard'] 
                                    ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                                    : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800 font-medium' }}">
                                 <div class="flex items-center gap-3 min-w-0">
                                     <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                                         {{ $checklist['workboard'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                                         @if($checklist['workboard'])
                                             <i data-lucide="check" class="w-4 h-4"></i>
                                         @else
                                             7
                                         @endif
                                     </div>
                                     <div class="min-w-0">
                                         <h4 class="text-sm font-bold truncate">7. Papan Kerja (Workboard)</h4>
                                         <p class="text-xs text-gray-400 truncate">Kirim folder kerja & link hasil foto</p>
                                     </div>
                                 </div>
                                 @if(!$checklist['workboard'])
                                     <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0"></i>
                                 @endif
                             </a>

                             <!-- Langkah 8 -->
                             <a href="{{ route('financial.index') }}" 
                                class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group
                                {{ $checklist['financial'] 
                                    ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                                    : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                                 <div class="flex items-center gap-3 min-w-0">
                                     <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                                         {{ $checklist['financial'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                                         @if($checklist['financial'])
                                             <i data-lucide="check" class="w-4 h-4"></i>
                                         @else
                                             8
                                         @endif
                                     </div>
                                     <div class="min-w-0">
                                         <h4 class="text-sm font-bold truncate">8. Laporan Keuangan</h4>
                                         <p class="text-xs text-gray-400 truncate">Pantau pendapatan & pengeluaran</p>
                                     </div>
                                 </div>
                                 @if(!$checklist['financial'])
                                     <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0"></i>
                                 @endif
                             </a>

                             <!-- Langkah 9 -->
                             <a href="{{ route('bookings.export') }}" 
                                class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group
                                {{ $checklist['export'] 
                                    ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                                    : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                                 <div class="flex items-center gap-3 min-w-0">
                                     <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                                         {{ $checklist['export'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                                         @if($checklist['export'])
                                             <i data-lucide="check" class="w-4 h-4"></i>
                                         @else
                                             9
                                         @endif
                                     </div>
                                     <div class="min-w-0">
                                         <h4 class="text-sm font-bold truncate">9. Unduh Excel Data</h4>
                                         <p class="text-xs text-gray-400 truncate">Ekspor data booking ke Excel</p>
                                     </div>
                                 </div>
                                 @if(!$checklist['export'])
                                     <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0"></i>
                                 @endif
                             </a>

                        </div>
                    </div>
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

            // Scroll ke panduan jika parameter show_help ada di URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('show_help')) {
                const card = document.getElementById('onboarding-guide-card');
                if (card) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Efek ring highlight biru berkedip agar admin langsung tahu area panduan
                    card.classList.add('ring-4', 'ring-blue-500/50', 'ring-offset-2');
                    setTimeout(() => {
                        card.classList.remove('ring-4', 'ring-blue-500/50', 'ring-offset-2');
                    }, 2500);
                }
            }
        });
    </script>
</x-app-layout>