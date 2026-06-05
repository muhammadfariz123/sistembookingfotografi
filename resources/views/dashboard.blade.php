{{-- resources/views/dashboard.blade.php --}}
{{-- ================================================
     Dashboard — hanya memanggil komponen
     
     Komponen dan tanggung jawabnya:
       summary-cards   → fetch summary, kartu status booking & pembayaran
       booking-link    → link untuk klien
       filter-panel    → search, filter, toggle tabel/kalender, tombol aksi
       booking-table   → tabel data booking + polling realtime
       booking-calendar→ kalender booking
       invoice-modal   → generate & download invoice
     ================================================ --}}
<x-app-layout>
    <div x-data="dashboardApp()"
         @set-view-mode.window="viewMode = $event.detail"
         class="px-4 sm:px-6 lg:px-7 py-7 bg-[#f5f7fb] min-h-screen overflow-x-hidden">

        {{-- Kartu summary: status booking + status pembayaran --}}
        <x-dashboard.summary-cards />

        {{-- Filter, toolbar, link klien --}}
        <x-dashboard.filter-panel />

        {{-- Tabel booking --}}
        <div x-show="viewMode === 'table'" x-transition class="mt-7">
            <x-dashboard.booking-table />
        </div>

        {{-- Kalender booking --}}
        <div x-show="viewMode === 'calendar'" x-transition class="mt-7" x-cloak>
            <x-dashboard.booking-calendar />
        </div>

        {{-- Modal: invoice --}}
        <x-dashboard.invoice-modal />

    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
    function dashboardApp() {
        return {
            viewMode: 'table',

            // Kalender helpers
            currentDate: new Date(),
            monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],

            init() {
                this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
                
                // === SMART POLLING 1 DETIK ===
                // Mengambil data senyap setiap 1 detik HANYA jika layar admin sedang dilihat
                let pollInterval;
                const startPolling = () => {
                    if(!pollInterval) {
                        pollInterval = setInterval(() => {
                            if(document.visibilityState === 'visible') {
                                window.dispatchEvent(new CustomEvent('reload-data-silent'));
                            }
                        }, 5000); // 1000 ms = 1 detik (Sensasi Real-time)
                    }
                };
                
                const stopPolling = () => {
                    clearInterval(pollInterval);
                    pollInterval = null;
                };

                // Deteksi jika admin pindah tab/minimize browser untuk menghemat server
                document.addEventListener('visibilitychange', () => {
                    if(document.visibilityState === 'visible') {
                        window.dispatchEvent(new CustomEvent('reload-data-silent')); 
                        startPolling();
                    } else {
                        stopPolling(); // Server diistirahatkan
                    }
                });

                startPolling();
            },

            get calendarTitle() {
                return `Kalender ${this.monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`
            },
            get calendarDates() {
                const y = this.currentDate.getFullYear(), m = this.currentDate.getMonth()
                let d = new Date(y, m, 1).getDay(); d = d === 0 ? 6 : d - 1
                const total = new Date(y, m + 1, 0).getDate()
                const dates = [...Array(d).fill(null)]
                for (let i = 1; i <= total; i++) dates.push(i)
                while (dates.length % 7) dates.push(null)
                return dates
            },
            prevMonth() {
                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1)
                this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
            },
            nextMonth() {
                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1)
                this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
            },
        }
    }

    document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons() })
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</x-app-layout>