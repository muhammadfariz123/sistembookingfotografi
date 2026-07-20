{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">
        
        {{-- HEADER SUMMARY & TINDAKAN CEPAT --}}
        <x-dashboard.dashboard-header />

        {{-- Jika Ingin Ditambahkan Fitur Tambahan (Misal Recent Booking) di Masa Depan, Letakkan Di Bawah Sini --}}
        
    </div>

    {{-- Script ringan untuk memuat icon --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons() })
    </script>
</x-app-layout>