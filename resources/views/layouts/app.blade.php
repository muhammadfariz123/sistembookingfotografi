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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#f5f7fb]">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white shadow-sm border-b border-gray-100">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            /*
            |--------------------------------------------------------------------------
            | SUCCESS ALERT
            |--------------------------------------------------------------------------
            */
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    confirmButtonColor: '#2563eb',
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-[28px]' }
                })
            @endif

            /*
            |--------------------------------------------------------------------------
            | ERROR ALERT
            |--------------------------------------------------------------------------
            */
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: @json(session('error')),
                    confirmButtonColor: '#dc2626',
                    customClass: { popup: 'rounded-[28px]' }
                })
            @endif
        })

        /*
        |--------------------------------------------------------------------------
        | DELETE CONFIRMATION GLOBAL
        |--------------------------------------------------------------------------
        */
        function confirmDelete(event, text = 'Data yang dihapus tidak bisa dikembalikan.') {
            event.preventDefault()
            Swal.fire({
                title: 'Hapus data?',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Merah untuk aksi hapus (UX lebih baik)
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-[28px]' }
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit()
                }
            })
            return false
        }
    </script>
</body>
</html>