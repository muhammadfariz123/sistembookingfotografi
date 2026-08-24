{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">
        
        {{-- HEADER SUMMARY & TINDAKAN CEPAT --}}
        <x-dashboard.dashboard-header :initialSummary="$initialSummary ?? null" />

        {{-- Jika Ingin Ditambahkan Fitur Tambahan (Misal Recent Booking) di Masa Depan, Letakkan Di Bawah Sini --}}
        
    </div>

    <script>
        document.addEventListener('turbo:load', () => { if (window.lucide) lucide.createIcons() })
    </script>
</x-app-layout>