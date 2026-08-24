{{-- resources/views/components/dashboard/dashboard-header.blade.php --}}
<div x-data="dashboardHeader(@js($initialSummary ?? null))" @reload-bookings.window="load()" @reload-data-silent.window="load()" class="w-full mb-7">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 items-stretch">

        {{-- KOLOM KIRI (2/3): INFORMASI KINERJA (STATISTIK) --}}
        <div class="xl:col-span-2 flex flex-col gap-5">
            
            {{-- 4 KOTAK STATISTIK UTAMA --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                
                {{-- 1. Booking Bulan Ini --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <i data-lucide="book-open" class="w-3.5 h-3.5 text-blue-500"></i> Booking <span x-text="summary.current_month_name"></span>
                    </p>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-2" x-text="summary.booking_this_month">0</h1>
                    
                    {{-- Dinamis: Pertumbuhan Booking --}}
                    <p class="text-[11px] font-medium w-fit px-2 py-0.5 rounded-full flex items-center gap-1"
                       :class="summary.booking_growth >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'">
                        <i :data-lucide="summary.booking_growth >= 0 ? 'trending-up' : 'trending-down'" class="w-3 h-3"></i> 
                        <span x-text="(summary.booking_growth >= 0 ? '+' : '') + summary.booking_growth + '%'"></span> vs bln lalu
                    </p>
                </div>

                {{-- 2. Revenue Bulan Ini --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <i data-lucide="banknote" class="w-3.5 h-3.5 text-emerald-500"></i> Revenue <span x-text="summary.current_month_name"></span>
                    </p>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-2 break-words" x-text="formatShortCurrency(summary.revenue_this_month)">Rp 0</h1>
                    
                    {{-- Dinamis: Pertumbuhan Revenue --}}
                    <p class="text-[11px] font-medium w-fit px-2 py-0.5 rounded-full flex items-center gap-1"
                       :class="summary.revenue_growth >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'">
                        <i :data-lucide="summary.revenue_growth >= 0 ? 'trending-up' : 'trending-down'" class="w-3 h-3"></i> 
                        <span x-text="(summary.revenue_growth >= 0 ? '+' : '') + summary.revenue_growth + '%'"></span> vs bln lalu
                    </p>
                </div>

                {{-- 3. Menunggu Pembayaran --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-orange-500"></i> Menunggu Bayar
                    </p>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-2" x-text="summary.pending_count">0</h1>
                    <p class="text-[11px] font-medium text-orange-600 bg-orange-50 w-fit px-2 py-0.5 rounded-full break-words">
                        <span x-text="formatShortCurrency(summary.pending_value)"></span> total
                    </p>
                </div>

                {{-- 4. Sesi Hari Ini --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
                    <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-purple-500"></i> Sesi Hari Ini
                    </p>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-2" x-text="summary.today_session_count">0</h1>
                    <p class="text-[11px] font-medium text-purple-600 bg-purple-50 w-fit px-2 py-0.5 rounded-full">
                        <span x-text="summary.today_session_confirmed"></span> terkonfirmasi &middot; Konversi <span x-text="summary.today_conversion_rate"></span>%
                    </p>
                </div>

            </div>

            {{-- DAFTAR JADWAL HARI INI (Bagian Bawah Kiri) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                    <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        📸 Jadwal Hari Ini
                    </h2>
                    <p class="text-[12px] font-semibold text-gray-400" x-text="summary.today_date_name"></p>
                </div>

                {{-- Jika Ada Jadwal --}}
                <template x-if="summary.today_schedules && summary.today_schedules.length > 0">
                    <div class="space-y-3 overflow-y-auto max-h-[160px] pr-1">
                        <template x-for="jadwal in summary.today_schedules" :key="jadwal.id">
                            <div class="flex items-center justify-between bg-gray-50/50 hover:bg-gray-50 border border-gray-100 p-3 rounded-xl transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex flex-col items-center justify-center font-bold">
                                        <span class="text-[12px] leading-none" x-text="(jadwal.booking_time || '00:00').split(':')[0]"></span>
                                        <span class="text-[10px] leading-none opacity-80" x-text="(jadwal.booking_time || '00:00').split(':')[1]"></span>
                                    </div>
                                    <div>
                                        <p class="text-[13px] font-bold text-gray-900 leading-tight" x-text="jadwal.client_name"></p>
                                        <p class="text-[11px] text-gray-500" x-text="jadwal.service_type?.name"></p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg"
                                    :class="jadwal.status === 'Dijadwalkan' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700'"
                                    x-text="jadwal.status">
                                </span>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Jika Kosong --}}
                <template x-if="!summary.today_schedules || summary.today_schedules.length === 0">
                    <div class="flex flex-col items-center justify-center h-[120px] opacity-70">
                        <span class="text-3xl mb-2">📅</span>
                        <p class="text-[13px] font-semibold text-gray-600">Tidak ada sesi hari ini</p>
                        <p class="text-[11px] text-gray-400">Waktunya istirahat! 🎉</p>
                    </div>
                </template>
            </div>

        </div>

        {{-- KOLOM KANAN (1/3): PANEL TINDAKAN UTAMA & LINK --}}
        <div class="xl:col-span-1 bg-white rounded-[24px] shadow-sm border border-gray-100 p-5 flex flex-col h-full">
            
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 shrink-0">Tindakan Cepat</h2>
            <div class="flex flex-col gap-2.5">
                <a href="{{ route('booking.public.form', ['ownerId' => Auth::id()]) }}" target="_blank" class="h-[46px] w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-[13px] flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Booking Baru
                </a>
                
                {{-- [DIUBAH]: Mengarah ke route bookings.calendar --}}
                <a href="{{ route('bookings.calendar') }}" class="h-[46px] w-full rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-bold text-[13px] flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i> Kalender
                </a>

                <a href="{{ route('service-types.index') }}" class="h-[46px] w-full rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-bold text-[13px] flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="briefcase" class="w-4 h-4 text-gray-400"></i> Kelola Layanan / Paket
                </a>
            </div>

            <hr class="border-gray-100 my-4">

            <div class="mt-auto">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2.5">Tautan Form Klien Publik</h2>
                <x-dashboard.booking-link />
            </div>

        </div>

    </div>
</div>

<script>
function dashboardHeader(initialSummary = null) {
    return {
        summary: initialSummary || { 
            current_month_name: 'Bulan Ini',
            today_date_name: 'Hari Ini',
            booking_this_month: 0, 
            booking_growth: 0,
            revenue_this_month: 0, 
            revenue_growth: 0,
            pending_count: 0, 
            pending_value: 0,
            today_session_count: 0,
            today_session_confirmed: 0,
            today_conversion_rate: 0,
            today_schedules: []
        }, 

        init() { 
            if (!initialSummary) {
                this.load();
            }
        },

        async load() {
            try {
                const res = await fetch('/bookings', { headers: { 'Accept': 'application/json' } })
                const r   = await res.json()
                if (r.summary) {
                    this.summary = {
                        current_month_name: r.summary.current_month_name ?? '...',
                        today_date_name: r.summary.today_date_name ?? '...',
                        booking_this_month: r.summary.booking_this_month ?? 0,
                        booking_growth: r.summary.booking_growth ?? 0,
                        revenue_this_month: r.summary.revenue_this_month ?? 0,
                        revenue_growth: r.summary.revenue_growth ?? 0,
                        pending_count: r.summary.pending_count ?? 0,
                        pending_value: r.summary.pending_value ?? 0,
                        today_session_count: r.summary.today_session_count ?? 0,
                        today_session_confirmed: r.summary.today_session_confirmed ?? 0,
                        today_conversion_rate: r.summary.today_conversion_rate ?? 0,
                        today_schedules: r.summary.today_schedules ?? []
                    }
                }
                this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
            } catch {}
        },

        formatShortCurrency(value) {
            if (!value || value === 0) return 'Rp 0k';
            if (value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1).replace('.0', '') + 'M';
            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1).replace('.0', '') + 'jt';
            if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'k';
            return 'Rp ' + value;
        }
    }
}
</script>