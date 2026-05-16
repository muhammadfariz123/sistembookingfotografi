<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- SWEETALERT -->
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

                    customClass: {

                        popup: 'rounded-[28px]'

                    }

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

                    customClass: {

                        popup: 'rounded-[28px]'

                    }

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

                confirmButtonColor: '#2563eb',

                cancelButtonColor: '#d1d5db',

                confirmButtonText: 'Ya, Hapus',

                cancelButtonText: 'Batal',

                reverseButtons: true,

                customClass: {

                    popup: 'rounded-[28px]'

                }

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