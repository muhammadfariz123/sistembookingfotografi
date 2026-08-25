<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Rozi Photography') }}</title>

    <!-- Optimasi Jaringan -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest" defer></script>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Poppins', sans-serif !important; }
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: width 0.3s ease-in-out; }
    </style>

    {{-- PENYIMPANAN SEMENTARA PESAN FLASH UNTUK MENCEGAH BUG POPUP MUNCUL TERUS --}}
    @if (session('success'))
        <meta name="flash-success" content="{{ session('success') }}">
    @endif
    @if (session('error'))
        <meta name="flash-error" content="{{ session('error') }}">
    @endif
</head>
<body class="antialiased bg-[#f5f7fb]">

    {{-- Wrapper Utama --}}
    <div x-data class="flex h-screen overflow-hidden">

        {{-- Sidebar Kiri --}}
        @include('layouts.navigation')

        {{-- Area Konten Utama (Kanan) --}}
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

            <header class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
                <div class="flex items-center gap-3">
                    <button @click="$store.sidebar.mobileOpen = true" class="p-2 -ml-2 rounded-xl text-gray-600 hover:bg-gray-100 transition focus:outline-none">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <span class="font-extrabold text-[18px] text-blue-600">Rozi Photo</span>
                </div>
            </header>

            @isset($header)
                <header class="bg-white shadow-sm border-b border-gray-100 hidden lg:block">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="w-full">
                {{ $slot }}
            </main>

        </div>
    </div>

    <script type="module">
        const initApp = () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Membaca pesan dari Meta Tag, jalankan Swal, lalu langsung HAPUS!
            const successMeta = document.querySelector('meta[name="flash-success"]');
            if (successMeta) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: successMeta.content, confirmButtonColor: '#2563eb', timer: 2500, showConfirmButton: false, customClass: { popup: 'rounded-[28px]' } });
                successMeta.remove(); // Kunci perbaikannya di sini (mencegah loop Turbo Cache)
            }

            const errorMeta = document.querySelector('meta[name="flash-error"]');
            if (errorMeta) {
                Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: errorMeta.content, confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-[28px]' } });
                errorMeta.remove(); // Kunci perbaikannya di sini
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initApp);
        } else {
            initApp();
        }

        document.addEventListener('turbo:load', initApp);
        document.addEventListener('alpine:updated', () => {
            if (window.lucide) window.lucide.createIcons();
        });

        window.confirmDelete = function(event, text = 'Data yang dihapus tidak bisa dikembalikan.') {
            event.preventDefault();
            Swal.fire({ title: 'Hapus data?', text: text, icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', reverseButtons: true, customClass: { popup: 'rounded-[28px]' } }).then((result) => {
                if (result.isConfirmed) event.target.submit();
            })
            return false;
        };
    </script>
</body>
</html>