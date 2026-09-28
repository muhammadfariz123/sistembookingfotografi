{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">
        
        @if($showOnboarding)
        @php
            $completedCount = collect($checklist)->filter()->count();
            $percent = ($completedCount / 8) * 100;
        @endphp
        
        <div x-data="{ expanded: {{ $completedCount >= 6 ? 'false' : 'true' }}, dismissed: localStorage.getItem('onboarding_dismissed') === 'true' }" 
             x-show="!dismissed"
             class="bg-white border border-gray-200 rounded-[20px] shadow-sm mb-7 overflow-hidden transition-all duration-300">
            <!-- Header (Clickable) -->
            <div class="w-full text-left p-4 md:p-6 flex flex-col sm:flex-row sm:items-center justify-between hover:bg-gray-50 transition cursor-pointer gap-4" @click="expanded = !expanded">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i data-lucide="compass" class="w-5 h-5 md:w-6 md:h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-[16px] md:text-xl font-bold text-gray-900 leading-tight mb-1">Panduan Pengoperasian</h2>
                        <div class="flex items-center gap-2 text-[12px] md:text-sm text-gray-500 font-medium" x-show="!expanded">
                            <span class="text-blue-600 font-bold">{{ $completedCount }} dari 8 Selesai</span>
                            <span>&bull;</span>
                            <span>Klik untuk melanjutkan</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <!-- Tombol Expand/Collapse -->
                    <button class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full text-[13px] font-bold text-gray-600 transition-colors">
                        <span x-text="expanded ? 'Tutup' : 'Lihat'"></span>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''"></i>
                    </button>
                    <!-- Tombol Dismiss -->
                    <button @click.stop="dismissed = true; localStorage.setItem('onboarding_dismissed', 'true')" class="flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 rounded-full text-[13px] font-bold text-red-600 transition-colors" title="Sembunyikan Panduan secara permanen">
                        <i data-lucide="x" class="w-4 h-4"></i> <span class="hidden sm:inline">Sembunyikan</span>
                    </button>
                </div>
            </div>
            
            <!-- Isi Panduan -->
            <div x-show="expanded" class="px-4 md:px-6 pb-4 md:pb-6 border-t border-gray-100 pt-4 md:pt-6">
                <p class="text-[13px] md:text-sm text-gray-500 mb-6 leading-relaxed">
                    Selesaikan langkah-langkah di bawah ini untuk mengonfigurasi dan mengoperasikan sistem booking fotografi Anda secara terstruktur. Pengecekan otomatis didasarkan pada data nyata di database.
                </p>

                <!-- Progress Bar -->
                <div class="bg-gray-50 border border-gray-100 p-4 rounded-2xl mb-6">
                    <div class="flex flex-col gap-1 mb-3">
                        <span class="text-[12px] font-semibold text-gray-600">Status Setup Sistem</span>
                        <span class="text-blue-600 text-[14px] font-bold">{{ $completedCount }} dari 8 Langkah Selesai ({{ round($percent) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <!-- Grid Steps -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 items-stretch">
                
                <!-- Langkah 1 -->
                <a href="{{ route('company-setting.edit') }}" 
                   class="flex items-center justify-between p-3 md:p-4 rounded-xl border transition-all duration-300 group h-full
                   {{ $checklist['settings'] 
                       ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                       : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800 font-medium' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                            {{ $checklist['settings'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                            @if($checklist['settings'])
                                <i data-lucide="check" class="w-4 h-4"></i>
                            @else
                                1
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold leading-tight mb-0.5">1. Pengaturan Toko</h4>
                            <p class="text-[11px] text-gray-400 leading-snug">Nama studio, kontak, & QRIS</p>
                        </div>
                    </div>
                    @if(!$checklist['settings'])
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0 ml-2"></i>
                    @endif
                </a>

                <!-- Langkah 2 -->
                <a href="{{ route('service-categories.index') }}" 
                   class="flex items-center justify-between p-3 md:p-4 rounded-xl border transition-all duration-300 group h-full
                   {{ $checklist['categories'] 
                       ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                       : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                            {{ $checklist['categories'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                            @if($checklist['categories'])
                                <i data-lucide="check" class="w-4 h-4"></i>
                            @else
                                2
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold leading-tight mb-0.5">2. Kategori & Portofolio</h4>
                            <p class="text-[11px] text-gray-400 leading-snug">Buat kategori & upload foto</p>
                        </div>
                    </div>
                    @if(!$checklist['categories'])
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0 ml-2"></i>
                    @endif
                </a>

                <!-- Langkah 3 -->
                <a href="{{ route('service-types.index') }}" 
                   class="flex items-center justify-between p-3 md:p-4 rounded-xl border transition-all duration-300 group h-full
                   {{ $checklist['services'] 
                       ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                       : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                            {{ $checklist['services'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                            @if($checklist['services'])
                                <i data-lucide="check" class="w-4 h-4"></i>
                            @else
                                3
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold leading-tight mb-0.5">3. Layanan & Paket</h4>
                            <p class="text-[11px] text-gray-400 leading-snug">Tentukan harga & detail paket</p>
                        </div>
                    </div>
                    @if(!$checklist['services'])
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0 ml-2"></i>
                    @endif
                </a>

                <!-- Langkah 4 -->
                <a href="{{ route('bookings.listPage') }}" 
                   class="flex items-center justify-between p-3 md:p-4 rounded-xl border transition-all duration-300 group h-full
                   {{ $checklist['bookings'] 
                       ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                       : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                             {{ $checklist['bookings'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                             @if($checklist['bookings'])
                                 <i data-lucide="check" class="w-4 h-4"></i>
                             @else
                                 4
                             @endif
                         </div>
                         <div>
                             <h4 class="text-sm font-bold leading-tight mb-0.5">4. Pantau Daftar Booking</h4>
                             <p class="text-[11px] text-gray-400 leading-snug">Pantau pesanan masuk & status sesi</p>
                         </div>
                     </div>
                     @if(!$checklist['bookings'])
                         <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0 ml-2"></i>
                     @endif
                 </a>

                 <!-- Langkah 5 -->
                 <a href="{{ route('transactions.index') }}" 
                    class="flex items-center justify-between p-3 md:p-4 rounded-xl border transition-all duration-300 group h-full
                    {{ $checklist['transactions'] 
                        ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                        : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                     <div class="flex items-center gap-3">
                         <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                             {{ $checklist['transactions'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                             @if($checklist['transactions'])
                                 <i data-lucide="check" class="w-4 h-4"></i>
                             @else
                                 5
                             @endif
                         </div>
                         <div>
                             <h4 class="text-sm font-bold leading-tight mb-0.5">5. Daftar Transaksi</h4>
                             <p class="text-[11px] text-gray-400 leading-snug">Konfirmasi bukti bayar klien</p>
                         </div>
                     </div>
                     @if(!$checklist['transactions'])
                         <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0 ml-2"></i>
                     @endif
                 </a>

                 <!-- Langkah 6 -->
                 <a href="{{ route('bookings.calendar') }}" 
                    class="flex items-center justify-between p-3 md:p-4 rounded-xl border transition-all duration-300 group h-full
                    {{ $checklist['calendar'] 
                        ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                        : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                     <div class="flex items-center gap-3">
                         <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                             {{ $checklist['calendar'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                             @if($checklist['calendar'])
                                 <i data-lucide="check" class="w-4 h-4"></i>
                             @else
                                 6
                             @endif
                         </div>
                         <div>
                             <h4 class="text-sm font-bold leading-tight mb-0.5">6. Kalender Jadwal</h4>
                             <p class="text-[11px] text-gray-400 leading-snug">Pantau jadwal sesi foto</p>
                         </div>
                     </div>
                     @if(!$checklist['calendar'])
                         <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0 ml-2"></i>
                     @endif
                 </a>

                 <!-- Langkah 7 -->
                 <a href="{{ route('workboard.index') }}" 
                    class="flex items-center justify-between p-3 md:p-4 rounded-xl border transition-all duration-300 group h-full
                    {{ $checklist['workboard'] 
                        ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                        : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800 font-medium' }}">
                     <div class="flex items-center gap-3">
                         <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                             {{ $checklist['workboard'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                             @if($checklist['workboard'])
                                 <i data-lucide="check" class="w-4 h-4"></i>
                             @else
                                 7
                             @endif
                         </div>
                         <div>
                             <h4 class="text-sm font-bold leading-tight mb-0.5">7. Papan Kerja</h4>
                             <p class="text-[11px] text-gray-400 leading-snug">Kirim folder kerja & link hasil</p>
                         </div>
                     </div>
                     @if(!$checklist['workboard'])
                         <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0 ml-2"></i>
                     @endif
                 </a>

                 <!-- Langkah 8 -->
                 <a href="{{ route('financial.index') }}" 
                    class="flex items-center justify-between p-3 md:p-4 rounded-xl border transition-all duration-300 group h-full
                    {{ $checklist['financial'] 
                        ? 'bg-emerald-50/30 border-emerald-100 text-emerald-800 font-medium' 
                        : 'bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm text-gray-800' }}">
                     <div class="flex items-center gap-3">
                         <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center font-bold text-sm
                             {{ $checklist['financial'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600' }}">
                             @if($checklist['financial'])
                                 <i data-lucide="check" class="w-4 h-4"></i>
                             @else
                                 8
                             @endif
                         </div>
                         <div>
                             <h4 class="text-sm font-bold leading-tight mb-0.5">8. Laporan Keuangan</h4>
                             <p class="text-[11px] text-gray-400 leading-snug">Pantau pendapatan & pengeluaran</p>
                         </div>
                     </div>
                     @if(!$checklist['financial'])
                         <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition shrink-0 ml-2"></i>
                     @endif
                 </a>

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
        });
    </script>
</x-app-layout>