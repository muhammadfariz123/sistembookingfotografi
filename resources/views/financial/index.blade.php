<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Keuangan</title>
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    {{-- Styles & Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-white">
        {{-- HEADER --}}
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                {{-- LEFT --}}
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}"
                        class="text-gray-700 hover:text-blue-600 transition">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <div class="flex items-baseline gap-3">
                        <h1 class="text-[28px] font-bold text-emerald-600 leading-none">
                            Dashboard Keuangan
                        </h1>
                        <p class="text-sm text-gray-500">
                            {{ Auth::user()->name ?? Auth::user()->email }}
                        </p>
                    </div>
                </div>
                {{-- RIGHT --}}
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Export Excel --}}
                    <button
                        class="h-[40px] px-5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold text-sm flex items-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Export Excel
                    </button>
                    {{-- Filter Bulan --}}
                    <select
                        class="h-[40px] px-3 pr-8 rounded-lg border-2 border-blue-500 bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                        <option>Semua Bulan</option>
                        <option>Januari</option>
                        <option>Februari</option>
                        <option>Maret</option>
                        <option>April</option>
                        <option>Mei</option>
                        <option>Juni</option>
                        <option>Juli</option>
                        <option>Agustus</option>
                        <option>September</option>
                        <option>Oktober</option>
                        <option>November</option>
                        <option>Desember</option>
                    </select>
                    <span class="text-gray-400 text-sm">atau</span>
                    {{-- Tanggal Mulai --}}
                    <input
                        type="date"
                        class="h-[40px] px-3 rounded-lg border border-gray-300 bg-white text-sm text-gray-700">
                    <span class="text-gray-400 text-sm">–</span>
                    {{-- Tanggal Akhir --}}
                    <input
                        type="date"
                        class="h-[40px] px-3 rounded-lg border border-gray-300 bg-white text-sm text-gray-700">
                </div>
            </div>
        </div>
        {{-- CONTENT --}}
        <div class="px-4 sm:px-6 lg:px-8 pb-8">
            <div class="bg-white rounded-[20px] border border-gray-200 min-h-[480px] flex items-center justify-center">
                <div class="text-center max-w-xl px-6 py-12">
                    {{-- ICON --}}
                    <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center">
                        <i data-lucide="bar-chart-3" class="w-6 h-6 text-gray-400"></i>
                    </div>
                    {{-- TITLE --}}
                    <h2 class="mt-6 text-[22px] font-bold text-gray-900">
                        Belum Ada Data Keuangan
                    </h2>
                    {{-- DESCRIPTION --}}
                    <p class="mt-2 text-gray-500 text-[15px]">
                        Mulai dengan membuat booking pertama untuk melihat analisis keuangan.
                    </p>
                    {{-- BUTTON --}}
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center mt-6 h-[40px] px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- LUCIDE --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
        document.addEventListener('alpine:updated', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>