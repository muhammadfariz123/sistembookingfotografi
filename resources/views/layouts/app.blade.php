<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Rozi Photography') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="turbo-prefetch" content="true">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Poppins', sans-serif !important; }
        [x-cloak] { display: none !important; }
        /* Transisi agar pergerakan lebar sidebar halus */
        .sidebar-transition { transition: width 0.3s ease-in-out; }
    </style>
</head>
<body class="antialiased bg-[#f5f7fb]">

    {{-- Wrapper Utama --}}
    {{-- KUNCI PERBAIKAN: Tambahkan atribut "x-data" di sini agar Alpine.js aktif merespons klik di seluruh area layout --}}
    <div x-data class="flex h-screen overflow-hidden">

        {{-- Sidebar Kiri --}}
        @include('layouts.navigation')

        {{-- Area Konten Utama (Kanan) --}}
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

            {{-- Tombol Hamburger untuk Mobile --}}
            <header class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
                <div class="flex items-center gap-3">
                    {{-- Saat diklik, buka sidebar versi mobile --}}
                    <button @click="$store.sidebar.mobileOpen = true" class="p-2 -ml-2 rounded-xl text-gray-600 hover:bg-gray-100 transition focus:outline-none">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <span class="font-extrabold text-[18px] text-blue-600">Rozi Photo</span>
                </div>
            </header>

            {{-- Header Halaman (Desktop) --}}
            @isset($header)
                <header class="bg-white shadow-sm border-b border-gray-100 hidden lg:block">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Konten Utama --}}
            <main class="w-full">
                {{ $slot }}
            </main>

        </div>
    </div>

    <script type="module">
        document.addEventListener('turbo:load', () => {
            lucide.createIcons();

            @if (session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: '#2563eb', timer: 2500, showConfirmButton: false, customClass: { popup: 'rounded-[28px]' } })
            @endif
            @if (session('error'))
                Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: @json(session('error')), confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-[28px]' } })
            @endif
        });
        document.addEventListener('alpine:updated', () => { lucide.createIcons(); });
        function confirmDelete(event, text = 'Data yang dihapus tidak bisa dikembalikan.') {
            event.preventDefault();
            Swal.fire({ title: 'Hapus data?', text: text, icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', reverseButtons: true, customClass: { popup: 'rounded-[28px]' } }).then((result) => {
                if (result.isConfirmed) event.target.submit();
            })
            return false;
        }
    </script>
</body>
</html>