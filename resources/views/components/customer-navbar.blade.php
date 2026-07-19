@props(['owner', 'companySetting', 'ownerId', 'showHome' => false])

<nav class="bg-white py-4 px-6 md:px-12 shadow-sm sticky top-0 z-50">
    <div class="flex items-center justify-between md:justify-center relative">
        
        {{-- LOGO (Kiri di Mobile, Absolute Kiri di Desktop) --}}
        <div class="text-xl font-extrabold text-brand tracking-tight md:absolute md:left-0">
            {{ $companySetting?->company_name ?? $owner->name }}
        </div>

        {{-- TOMBOL HAMBURGER (Hanya tampil di layar kecil/HP) --}}
        <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-600 hover:text-brand focus:outline-none transition">
            <svg id="icon-open" class="w-6 h-6 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        {{-- NAVIGASI DESKTOP (Otomatis di Tengah) --}}
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-500">
            @if($showHome)
                <a href="{{ route('booking.public.show', $ownerId) }}" class="hover:text-brand transition">Beranda</a>
            @endif
            
            <a href="{{ route('booking.public.show', $ownerId) }}#paket" class="hover:text-brand transition">Paket</a>
            <a href="{{ route('booking.check.page') }}" class="hover:text-brand transition">Cek Booking</a>
        </div>
    </div>

    {{-- NAVIGASI MOBILE (Dropdown) --}}
    <div id="mobile-menu" class="hidden md:hidden mt-4 pt-4 border-t border-gray-100 flex-col gap-2 text-sm font-medium text-gray-500 text-center pb-2">
        @if($showHome)
            <a href="{{ route('booking.public.show', $ownerId) }}" class="block py-2 hover:bg-gray-50 hover:text-brand rounded-lg transition">Beranda</a>
        @endif
        <a href="{{ route('booking.public.show', $ownerId) }}#paket" class="block py-2 hover:bg-gray-50 hover:text-brand rounded-lg transition">Paket</a>
        <a href="{{ route('booking.check.page') }}" class="block py-2 hover:bg-gray-50 hover:text-brand rounded-lg transition">Cek Booking</a>
    </div>
</nav>

{{-- SCRIPT UNTUK TOGGLE MENU MOBILE --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');

        if(btn) {
            btn.addEventListener('click', function() {
                if (menu.classList.contains('hidden')) {
                    // Buka Menu
                    menu.classList.remove('hidden');
                    menu.classList.add('flex');
                    iconOpen.classList.replace('block', 'hidden');
                    iconClose.classList.replace('hidden', 'block');
                } else {
                    // Tutup Menu
                    menu.classList.add('hidden');
                    menu.classList.remove('flex');
                    iconOpen.classList.replace('hidden', 'block');
                    iconClose.classList.replace('block', 'hidden');
                }
            });
        }
    });
</script>