{{-- resources/views/financial/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Keuangan</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Menimpa font bawaan menjadi Poppins */
        body { 
            font-family: 'Poppins', sans-serif !important; 
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="antialiased bg-[#f5f7fb]">
    <div x-data="financialPage()" class="min-h-screen">

        {{-- HEADER --}}
        <div class="px-4 sm:px-6 lg:px-8 py-5 bg-white border-b border-gray-100 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-blue-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="flex items-baseline gap-3">
                        <h1 class="text-[22px] font-bold text-emerald-600 leading-none">Dashboard Keuangan</h1>
                        <p class="text-sm text-gray-400 font-medium">{{ Auth::user()->name ?? Auth::user()->email }}</p>
                    </div>
                </div>

                {{-- FORM FILTER SIMPLE (HANYA BULAN) --}}
                <form method="GET" action="{{ route('financial.index') }}" class="flex flex-wrap items-center gap-2">

                    <button type="submit" name="export" value="1"
                        class="h-[38px] px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm flex items-center gap-2 transition mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Excel
                    </button>

                    {{-- Filter Bulan (Otomatis Submit) --}}
                    <select name="month" onchange="this.form.submit()"
                        class="h-[38px] px-3 pr-8 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $i => $m)
                            <option value="{{ $i + 1 }}" {{ request('month') == $i + 1 ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>

                    {{-- Tombol Reset (Hanya muncul jika ada filter bulan yang terisi) --}}
                    @if(request()->filled('month'))
                        <a href="{{ route('financial.index') }}"
                            class="h-[38px] px-4 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-sm flex items-center transition ml-1"
                            title="Hapus Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 pb-10 pt-6 space-y-6">

            {{-- EMPTY STATE --}}
            @if($bookings->count() === 0 && !request()->filled('month'))
                <div
                    class="bg-white rounded-[28px] border border-gray-100 min-h-[480px] flex flex-col items-center justify-center p-8 shadow-sm mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-300 mb-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h1 class="text-[20px] sm:text-[24px] font-bold text-gray-400">Belum ada data keuangan</h1>
                    <p class="text-gray-400 mt-2 text-[13px] sm:text-[14px] max-w-sm text-center">
                        Mulai dengan membuat booking pertama untuk melihat laporan dan analisis keuangan Anda di sini.
                    </p>
                    <a href="{{ route('dashboard') }}"
                        class="mt-6 inline-flex items-center gap-2 h-[44px] px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            @else

              {{-- ══════════════════════════════════════════════
                SUMMARY CARDS DENGAN INFO RINGKAS & JELAS
                ══════════════════════════════════════════════ --}}
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">

                    {{-- Total Revenue --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                        <div class="min-w-0">
                            <p class="text-[12px] text-gray-500 font-semibold">Total Revenue</p>
                            <p class="text-[10px] text-gray-400 leading-tight mt-0.5">Total keseluruhan harga dari semua booking masuk</p>
                            <p class="text-[16px] font-bold text-gray-900 mt-1 truncate">
                                Rp {{ number_format($revenue, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Sudah Diterima --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                        <div class="min-w-0">
                            <p class="text-[12px] text-gray-500 font-semibold">Sudah Diterima</p>
                            <p class="text-[10px] text-gray-400 leading-tight mt-0.5">Uang pembayaran dari klien yang sudah masuk</p>
                            <p class="text-[16px] font-bold text-emerald-600 mt-1 truncate">
                                Rp {{ number_format($sudahDiterima, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Sisa Tagihan (Dipindah ke Urutan 3 agar jadi Induk) --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                        <div class="min-w-0">
                            <p class="text-[12px] text-gray-500 font-semibold">Total Piutang</p>
                            <p class="text-[10px] text-gray-400 leading-tight mt-0.5">Total sisa uang yang masih harus dilunasi klien</p>
                            <p class="text-[16px] font-bold text-red-500 mt-1 truncate">
                                Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Belum Dibayar (Menjadi Rincian / Peringatan) --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                        <div class="min-w-0">
                            <p class="text-[12px] text-gray-500 font-semibold">Tagihan Belum DP</p>
                            <p class="text-[10px] text-gray-400 leading-tight mt-0.5">Bagian dari piutang yang sama sekali belum DP</p>
                            <p class="text-[16px] font-bold text-orange-500 mt-1 truncate">
                                Rp {{ number_format($belumDibayar, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Pemasukan Tambahan --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                        <div class="min-w-0">
                            <p class="text-[12px] text-gray-500 font-semibold">Pemasukan Lain</p>
                            <p class="text-[10px] text-gray-400 leading-tight mt-0.5">Pemasukan uang di luar dari layanan booking</p>
                            <p class="text-[16px] font-bold text-blue-600 mt-1 truncate">
                                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </div>

                    {{-- Total Pengeluaran --}}
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                        <div class="min-w-0">
                            <p class="text-[12px] text-gray-500 font-semibold">Pengeluaran</p>
                            <p class="text-[10px] text-gray-400 leading-tight mt-0.5">Segala biaya operasional & belanja usaha</p>
                            <p class="text-[16px] font-bold text-red-600 mt-1 truncate">
                                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Logika Penentuan Label Periode yang Bersih --}}
                @php
                    $periodLabel = 'Semua Waktu';
                    if (request()->filled('month') && is_numeric(request('month'))) {
                        $periodLabel = \Carbon\Carbon::create()->month((int) request('month'))->locale('id')->monthName . ' ' . request('year', now()->year);
                    }
                @endphp

                {{-- ══════════════════════════════════════════════
                LABA BERSIH CARD
                ══════════════════════════════════════════════ --}}
                <div
                    class="rounded-2xl p-6 {{ $labaBersih >= 0 ? 'bg-gradient-to-r from-emerald-500 to-green-600' : 'bg-gradient-to-r from-red-500 to-rose-600' }} text-white relative overflow-hidden shadow-md">
                    <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/10 -translate-y-1/2 translate-x-1/2">
                    </div>
                    <div class="absolute bottom-0 left-20 w-24 h-24 rounded-full bg-white/5"></div>
                    <div class="relative flex items-start justify-between">
                        <div>
                            <p class="text-white/90 text-[14px] font-semibold tracking-wide">Laba Bersih Perusahaan</p>
                            <p class="text-white/80 text-[12px] mt-0.5 max-w-sm">Keuntungan bersih dari uang yang berhasil masuk dikurangi seluruh pengeluaran operasional.</p>
                            <p class="text-[34px] font-bold mt-2 leading-none">
                                {{ $labaBersih < 0 ? '- ' : '' }}Rp {{ number_format(abs($labaBersih), 0, ',', '.') }}
                            </p>
                            <p class="text-white/90 text-[13px] font-medium mt-3">
                                Status: <span class="font-bold underline decoration-2 underline-offset-4">{{ $labaBersih >= 0 ? 'Untung' : 'Rugi' }}</span> &mdash;
                                Periode: {{ $periodLabel }}
                            </p>
                            
                            {{-- Penjabaran Perhitungan --}}
                            <div class="mt-3 inline-block bg-white/10 rounded-xl px-3 py-2 border border-white/20">
                                <p class="text-white text-[11px] font-medium tracking-wide">
                                    <span class="opacity-80">Rincian Hitungan:</span><br>
                                    (Uang Masuk: Rp {{ number_format($sudahDiterima, 0, ',', '.') }}) 
                                    + (Pemasukan: Rp {{ number_format($totalPemasukan, 0, ',', '.') }}) 
                                    – (Pengeluaran: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }})
                                </p>
                            </div>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center shrink-0 shadow-sm backdrop-blur-sm">
                            @if($labaBersih >= 0)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- PEMASUKAN TAMBAHAN & PENGELUARAN --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[15px] font-bold text-gray-900">Pemasukan Tambahan</h3>
                            <div class="flex items-center gap-2">
                                @if($additionalIncomes->count() > 0)
                                    <span class="text-[12px] font-medium text-gray-500">Total: {{ $additionalIncomes->count() }} Data</span>
                                @endif
                                <button @click="showIncomeModal = true"
                                    class="h-[32px] px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-[12px] font-semibold flex items-center gap-1.5 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah
                                </button>
                            </div>
                        </div>
                        @if($additionalIncomes->isEmpty())
                            <div class="py-8 text-center text-gray-400 text-sm font-medium">Tidak ada pemasukan tambahan</div>
                        @else
                            <div class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto">
                                @foreach($additionalIncomes as $income)
                                    <div class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-gray-50 transition">
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $income->description }}</p>
                                            <p class="text-[11px] text-gray-400 font-medium">{{ $income->date->format('d M Y') }}</p>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            <p class="text-[14px] font-bold text-blue-600">
                                                Rp {{ number_format($income->amount, 0, ',', '.') }}
                                            </p>
                                            <button onclick="deleteIncome({{ $income->id }})"
                                                class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition" title="Hapus Pemasukan">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[15px] font-bold text-gray-900">Pengeluaran</h3>
                            <div class="flex items-center gap-2">
                                @if($expenses->count() > 0)
                                    <span class="text-[12px] font-medium text-gray-500">Total: {{ $expenses->count() }} Data</span>
                                @endif
                                <button @click="showExpenseModal = true"
                                    class="h-[32px] px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-[12px] font-semibold flex items-center gap-1.5 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah
                                </button>
                            </div>
                        </div>
                        @if($expenses->isEmpty())
                            <div class="py-8 text-center text-gray-400 text-sm font-medium">Tidak ada pengeluaran</div>
                        @else
                            <div class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto">
                                @foreach($expenses as $expense)
                                    <div class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-gray-50 transition">
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $expense->description }}
                                            </p>
                                            <p class="text-[11px] text-gray-400 font-medium">{{ $expense->date->format('d M Y') }}</p>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            <p class="text-[14px] font-bold text-red-600">
                                                Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                            </p>
                                            <button onclick="deleteExpense({{ $expense->id }})"
                                                class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition" title="Hapus Pengeluaran">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>


                {{-- STATUS PEMBAYARAN --}}
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-[15px] font-bold text-gray-900">Total Status Pembayaran Booking</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center border border-green-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-[14px] font-semibold text-gray-800">Booking Lunas</span>
                            </div>
                            <span class="text-[15px] font-bold text-green-700 bg-green-100 px-3 py-1 rounded-lg">{{ $statusCount['lunas'] }}</span>
                        </div>
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center border border-orange-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <span class="text-[14px] font-semibold text-gray-800">Booking DP (Cicilan)</span>
                            </div>
                            <span class="text-[15px] font-bold text-orange-700 bg-orange-100 px-3 py-1 rounded-lg">{{ $statusCount['dp'] }}</span>
                        </div>
                        <div class="px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-yellow-50 flex items-center justify-center border border-yellow-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <span class="text-[14px] font-semibold text-gray-800">Booking Belum Bayar</span>
                            </div>
                            <span class="text-[15px] font-bold text-yellow-700 bg-yellow-100 px-3 py-1 rounded-lg">{{ $statusCount['belum_bayar'] }}</span>
                        </div>
                    </div>
                </div>

            @endif
        </div>

        {{-- MODAL TAMBAH PEMASUKAN --}}
        <div x-show="showIncomeModal" x-cloak x-transition.opacity class="fixed inset-0 z-50">
            <div @click="showIncomeModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div @click.stop class="bg-white w-full max-w-md rounded-[24px] shadow-2xl p-7">
                    <h3 class="text-[20px] font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Tambah Pemasukan Lainnya</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Deskripsi / Keterangan</label>
                            <input type="text" x-model="incomeForm.description" placeholder="Contoh: Jasa editing tambahan"
                                class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Jumlah (Rp)</label>
                            <input type="number" x-model="incomeForm.amount" placeholder="0" min="1"
                                class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal Pemasukan</label>
                            <input type="date" x-model="incomeForm.date"
                                class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="flex gap-3 mt-8 pt-4 border-t border-gray-100">
                        <button @click="showIncomeModal = false"
                            class="flex-1 h-[44px] rounded-xl bg-white border border-gray-200 text-gray-700 font-semibold text-[14px] hover:bg-gray-50 transition shadow-sm">
                            Batal
                        </button>
                        <button @click="saveIncome()"
                            class="flex-1 h-[44px] rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] shadow-lg shadow-blue-600/20 transition">
                            Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL TAMBAH PENGELUARAN --}}
        <div x-show="showExpenseModal" x-cloak x-transition.opacity class="fixed inset-0 z-50">
            <div @click="showExpenseModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div @click.stop class="bg-white w-full max-w-md rounded-[24px] shadow-2xl p-7">
                    <h3 class="text-[20px] font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Tambah Pengeluaran</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Deskripsi Pengeluaran</label>
                            <input type="text" x-model="expenseForm.description" placeholder="Contoh: Beli alat tulis, bensin, dll"
                                class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] font-medium focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Jumlah (Rp)</label>
                            <input type="number" x-model="expenseForm.amount" placeholder="0" min="1"
                                class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] font-medium focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal Pengeluaran</label>
                            <input type="date" x-model="expenseForm.date"
                                class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] font-medium focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="flex gap-3 mt-8 pt-4 border-t border-gray-100">
                        <button @click="showExpenseModal = false"
                            class="flex-1 h-[44px] rounded-xl bg-white border border-gray-200 text-gray-700 font-semibold text-[14px] hover:bg-gray-50 transition shadow-sm">
                            Batal
                        </button>
                        <button @click="saveExpense()"
                            class="flex-1 h-[44px] rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-[14px] shadow-lg shadow-orange-500/20 transition">
                            Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        function financialPage() {
            return {
                showIncomeModal: false,
                showExpenseModal: false,
                incomeForm: { description: '', amount: '', date: '' },
                expenseForm: { description: '', amount: '', date: '' },
                async saveIncome() {
                    if (!this.incomeForm.description || !this.incomeForm.amount || !this.incomeForm.date) {
                        alert('Mohon isi semua data untuk Pemasukan Tambahan.')
                        return
                    }
                    try {
                        const res = await fetch('{{ route("financial.income.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(this.incomeForm)
                        })
                        const data = await res.json()
                        if (data.success) {
                            this.showIncomeModal = false
                            this.incomeForm = { description: '', amount: '', date: '' }
                            window.location.reload()
                        }
                    } catch (e) { alert('Terjadi kesalahan saat menyimpan data.') }
                },
                async saveExpense() {
                    if (!this.expenseForm.description || !this.expenseForm.amount || !this.expenseForm.date) {
                        alert('Mohon isi semua data untuk Pengeluaran.')
                        return
                    }
                    try {
                        const res = await fetch('{{ route("financial.expense.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(this.expenseForm)
                        })
                        const data = await res.json()
                        if (data.success) {
                            this.showExpenseModal = false
                            this.expenseForm = { description: '', amount: '', date: '' }
                            window.location.reload()
                        }
                    } catch (e) { alert('Terjadi kesalahan saat menyimpan data.') }
                }
            }
        }
        async function deleteIncome(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus data pemasukan ini?')) return
            const res = await fetch(`/financial/income/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            })
            const data = await res.json()
            if (data.success) window.location.reload()
        }
        async function deleteExpense(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus data pengeluaran ini?')) return
            const res = await fetch(`/financial/expense/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            })
            const data = await res.json()
            if (data.success) window.location.reload()
        }
    </script>
</body>
</html>