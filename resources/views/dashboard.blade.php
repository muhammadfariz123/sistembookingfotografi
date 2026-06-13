{{-- resources/views/dashboard.blade.php --}}
{{-- ================================================
     Dashboard — hanya memanggil komponen utama
     
     Komponen dan tanggung jawabnya:
       dashboard-header → fetch summary, kartu status, pencarian & aksi
       booking-table    → tabel data booking + polling realtime
       invoice-modal    → generate & download invoice
     ================================================ --}}
<x-app-layout>
    <div x-data="dashboardApp()"
         class="px-4 sm:px-6 lg:px-7 py-7 bg-[#f5f7fb] min-h-screen overflow-x-hidden">

        {{-- 1. Komponen Header: Kartu Summary, Pencarian & Tombol Aksi --}}
        <x-dashboard.dashboard-header />

        {{-- 2. Area Tabel Booking (Diberi id agar auto-scroll berfungsi) --}}
        <div id="tabel-booking" class="mt-7">
            <x-dashboard.booking-table />
        </div>

        {{-- 3. Modal: Generate Invoice --}}
        <x-dashboard.invoice-modal />

    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
    function dashboardApp() {
        return {
            init() {
                // Inisialisasi ikon Lucide
                this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
                
                // === SMART POLLING ===
                // Mengambil data senyap setiap 5 detik HANYA jika layar admin sedang dilihat
                let pollInterval;
                const startPolling = () => {
                    if(!pollInterval) {
                        pollInterval = setInterval(() => {
                            if(document.visibilityState === 'visible') {
                                window.dispatchEvent(new CustomEvent('reload-data-silent'));
                            }
                        }, 5000); // 5000 ms = 5 detik
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

                startPolling(); // Mulai polling saat pertama kali dimuat
            }
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