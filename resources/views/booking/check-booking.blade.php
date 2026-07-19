<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Booking</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-brand { background-color: #f59e0b; }
        .text-brand { color: #f59e0b; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

    <nav class="w-full bg-white border-b border-gray-100 py-4 px-6 flex justify-between items-center">
        <a href="{{ route('booking.public.show', $ownerId) }}" class="text-sm text-gray-500 hover:text-brand flex items-center gap-1 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Beranda
        </a>

        <div class="text-right">
            <h1 class="text-sm font-bold text-gray-900">{{ $businessName ?? 'Nama Usaha Anda' }}</h1>
        </div>
    </nav>

    <main class="flex items-center justify-center min-h-[75vh] p-4">
        <div class="w-full max-w-sm">

            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                    </svg>
                </div>
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">Cek Status Booking</h2>
                <p class="text-sm text-gray-400 mt-2">Masukkan kode booking dan email yang kamu gunakan saat booking.</p>
            </div>

            {{-- PESAN ERROR JIKA KODE/EMAIL TIDAK DITEMUKAN --}}
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 mb-4 text-sm font-medium text-center shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <form action="{{ route('booking.check.result') }}" method="GET" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">Kode Booking</label>
                        <input type="text" name="booking_code" required placeholder="Contoh: BKG-20260718-C6DD" value="{{ old('booking_code') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-amber-500 outline-none transition text-sm placeholder-gray-400 font-mono">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">Email</label>
                        <input type="email" name="email" required placeholder="email@kamu.com" value="{{ old('email') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-amber-500 outline-none transition text-sm placeholder-gray-400">
                    </div>

                    <button type="submit" class="w-full bg-brand text-white py-3.5 mt-2 rounded-xl font-bold text-sm hover:opacity-90 transition shadow-md flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Cari Booking
                    </button>
                </form>
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">Kode booking dikirim ke email kamu setelah mengisi form.</p>
                <p class="mt-6 text-[11px] text-gray-300">Powered by BookPhoto</p>
            </div>
        </div>
    </main>
</body>
</html>