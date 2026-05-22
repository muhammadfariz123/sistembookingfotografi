{{-- resources/views/dashboard.blade.php --}}
{{-- ================================================
     Dashboard — hanya memanggil komponen
     
     Komponen dan tanggung jawabnya:
       summary-cards   → fetch summary, kartu status booking & pembayaran
       booking-link    → link untuk klien
       filter-panel    → search, filter, toggle tabel/kalender, tombol aksi
       booking-table   → tabel data booking + polling realtime
       booking-calendar→ kalender booking
       booking-modal   → form tambah/edit booking + kalkulasi TPS
       invoice-modal   → generate & download invoice
       company-settings→ pengaturan perusahaan & rekening bank
     
     JS di sini: dashboardApp()
       Hanya menangani: viewMode, modal state
       Semua filter sudah pindah ke filter-panel
       Summary sudah pindah ke summary-cards
     ================================================ --}}
<x-app-layout>
    <div x-data="dashboardApp()"
         @open-company-settings.window="openCompanySettingsModal()"
         @set-view-mode.window="viewMode = $event.detail"
         @open-booking-modal.window="openBookingModal()"
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

        {{-- Modal: tambah/edit booking --}}
        @include('components.dashboard.booking-modal')

        {{-- Modal: invoice --}}
        <x-dashboard.invoice-modal />

        {{-- Modal: pengaturan perusahaan --}}
        <x-dashboard.company-settings-modal />
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
    // dashboardApp() — minimal, hanya kelola viewMode dan modal
    function dashboardApp() {
        return {
            viewMode: 'table',
            showBookingModal:         false,
            showCompanySettingsModal: false,

            // Kalender helpers — dipakai booking-calendar.blade.php
            currentDate: new Date(),
            monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],

            init() {
                this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
            },

            openBookingModal() {
                this.showBookingModal = true
                document.body.classList.add('overflow-hidden')
                this.$nextTick(() => {
                    if (window.lucide) lucide.createIcons()
                    const fd = document.getElementById('booking-form')?._x_dataStack?.[0]
                    if (fd) fd._editMode ? (fd._editMode = false) : fd.resetToCreate?.()
                })
            },
            closeBookingModal() {
                this.showBookingModal = false
                document.body.classList.remove('overflow-hidden')
                this.$nextTick(() => {
                    document.getElementById('booking-form')?._x_dataStack?.[0]?.resetToCreate?.()
                })
            },
            openCompanySettingsModal() {
                this.showCompanySettingsModal = true
                document.body.classList.add('overflow-hidden')
                this.$nextTick(() => {
                    if (window.lucide) lucide.createIcons()
                    window.dispatchEvent(new CustomEvent('reload-company-settings'))
                })
            },
            closeCompanySettingsModal() {
                this.showCompanySettingsModal = false
                document.body.classList.remove('overflow-hidden')
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