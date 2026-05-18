{{-- resources/views/financial/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Keuangan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white">
<div x-data="financialPage()" class="min-h-screen bg-white">

    {{-- ══════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════ --}}
    <div class="px-4 sm:px-6 lg:px-8 py-5 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {{-- LEFT --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-blue-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="flex items-baseline gap-3">
                    <h1 class="text-[22px] font-bold text-emerald-600 leading-none">Dashboard Keuangan</h1>
                    <p class="text-sm text-gray-400">{{ Auth::user()->name ?? Auth::user()->email }}</p>
                </div>
            </div>
            {{-- RIGHT: Filter --}}
            <form method="GET" action="{{ route('financial.index') }}"
                  class="flex flex-wrap items-center gap-2">
                <button type="submit" name="export" value="1"
                    class="h-[38px] px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm flex items-center gap-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Excel
                </button>
                <select name="month"
                    class="h-[38px] px-3 rounded-lg border-2 border-blue-400 bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">Semua Bulan</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $m)
                        <option value="{{ $i + 1 }}" {{ request('month') == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
                <span class="text-gray-400 text-sm">atau</span>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="h-[38px] px-3 rounded-lg border border-gray-300 bg-white text-sm text-gray-700">
                <span class="text-gray-400 text-sm">–</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="h-[38px] px-3 rounded-lg border border-gray-300 bg-white text-sm text-gray-700">
                <button type="submit"
                    class="h-[38px] px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition">
                    Filter
                </button>
                @if(request()->hasAny(['month','date_from','date_to']))
                    <a href="{{ route('financial.index') }}"
                        class="h-[38px] px-4 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm flex items-center transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         CONTENT
    ══════════════════════════════════════════════ --}}
    <div class="px-4 sm:px-6 lg:px-8 pb-10 pt-6 space-y-6">

        {{-- ── EMPTY STATE — tampil jika belum ada booking ─────── --}}
        @if($bookings->count() === 0)
            <div class="bg-white rounded-[20px] border border-gray-200 min-h-[480px] flex items-center justify-center">
                <div class="text-center max-w-xl px-6 py-12">
                    {{-- ICON --}}
                    <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
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
                        class="inline-flex items-center justify-center mt-6 h-[40px] px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>

        @else
        {{-- ── ADA DATA — tampilkan semua section ──────────────── --}}

        {{-- ══════════════════════════════════════════════
             SUMMARY CARDS — Rumus 3.6 s/d 3.9
        ══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
            {{-- Total Revenue (3.6) --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-500 font-medium">Total Revenue</p>
                    <p class="text-[15px] font-bold text-gray-900 mt-0.5 truncate">
                        Rp {{ number_format($revenue, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            {{-- Sudah Diterima (3.7) --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-500 font-medium">Sudah Diterima</p>
                    <p class="text-[15px] font-bold text-emerald-600 mt-0.5 truncate">
                        Rp {{ number_format($sudahDiterima, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            {{-- Belum Dibayar (3.8) --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-500 font-medium">Belum Dibayar</p>
                    <p class="text-[15px] font-bold text-orange-500 mt-0.5 truncate">
                        Rp {{ number_format($belumDibayar, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            {{-- Sisa Tagihan (3.9) --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-500 font-medium">Sisa Tagihan</p>
                    <p class="text-[15px] font-bold text-red-500 mt-0.5 truncate">
                        Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            {{-- Pemasukan Tambahan --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-500 font-medium">Pemasukan Tambahan</p>
                    <p class="text-[15px] font-bold text-blue-600 mt-0.5 truncate">
                        Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </div>
            {{-- Total Pengeluaran --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm">
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-500 font-medium">Total Pengeluaran</p>
                    <p class="text-[15px] font-bold text-red-600 mt-0.5 truncate">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             LABA BERSIH CARD — Rumus 3.10
        ══════════════════════════════════════════════ --}}
        <div class="rounded-2xl p-6 {{ $labaBersih >= 0 ? 'bg-gradient-to-r from-emerald-500 to-green-600' : 'bg-gradient-to-r from-red-500 to-rose-600' }} text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/10 -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-20 w-24 h-24 rounded-full bg-white/5"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-white/80 text-[13px] font-semibold">Laba Bersih</p>
                    <p class="text-white/70 text-[11px] mt-0.5">(Revenue Booking + Pemasukan Tambahan – Pengeluaran)</p>
                    <p class="text-[32px] font-bold mt-2 leading-none">
                        Rp {{ number_format(abs($labaBersih), 0, ',', '.') }}
                    </p>
                    <p class="text-white/80 text-[12px] mt-2">
                        {{ $labaBersih >= 0 ? 'Untung' : 'Rugi' }} &mdash;
                        Periode: {{ request('month') ? \Carbon\Carbon::create()->month(request('month'))->locale('id')->monthName : 'Semua Bulan' }}
                    </p>
                    <p class="text-white/70 text-[11px] mt-1">
                        Revenue: Rp {{ number_format($sudahDiterima, 0, ',', '.') }}
                        &nbsp;+&nbsp; Tambahan: Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                        &nbsp;–&nbsp; Pengeluaran: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             PEMASUKAN TAMBAHAN & PENGELUARAN
        ══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Pemasukan Tambahan --}}
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100">
                    <h3 class="text-[15px] font-semibold text-gray-900">Pemasukan Tambahan</h3>
                    <div class="flex items-center gap-2">
                        @if($additionalIncomes->count() > 0)
                            <span class="text-[12px] text-gray-500">Tampilkan ({{ $additionalIncomes->count() }})</span>
                        @endif
                        <button @click="showIncomeModal = true"
                            class="h-[32px] px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-semibold flex items-center gap-1.5 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Pemasukan
                        </button>
                    </div>
                </div>
                @if($additionalIncomes->isEmpty())
                    <div class="py-8 text-center text-gray-400 text-sm">Belum ada pemasukan tambahan</div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($additionalIncomes as $income)
                            <div class="px-5 py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-medium text-gray-800 truncate">{{ $income->description }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $income->date->format('d M Y') }}</p>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <p class="text-[13px] font-semibold text-blue-600">
                                        Rp {{ number_format($income->amount, 0, ',', '.') }}
                                    </p>
                                    <button onclick="deleteIncome({{ $income->id }})"
                                        class="w-6 h-6 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            {{-- Pengeluaran --}}
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100">
                    <h3 class="text-[15px] font-semibold text-gray-900">Pengeluaran</h3>
                    <div class="flex items-center gap-2">
                        @if($expenses->count() > 0)
                            <span class="text-[12px] text-gray-500">Tampilkan ({{ $expenses->count() }})</span>
                        @endif
                        <button @click="showExpenseModal = true"
                            class="h-[32px] px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-[13px] font-semibold flex items-center gap-1.5 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Pengeluaran
                        </button>
                    </div>
                </div>
                @if($expenses->isEmpty())
                    <div class="py-8 text-center text-gray-400 text-sm">Belum ada pengeluaran</div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($expenses as $expense)
                            <div class="px-5 py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-medium text-gray-800 truncate">{{ $expense->description }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $expense->date->format('d M Y') }}</p>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <p class="text-[13px] font-semibold text-red-600">
                                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                    </p>
                                    <button onclick="deleteExpense({{ $expense->id }})"
                                        class="w-6 h-6 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             TREN REVENUE BULANAN
        ══════════════════════════════════════════════ --}}
        @if($trendData->count() > 0)
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-[15px] font-semibold text-gray-900">Tren Revenue Bulanan</h3>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="px-5 py-4 space-y-3">
                @php $maxRevenue = $trendData->max('total') ?: 1; @endphp
                @foreach($trendData as $yearMonth => $data)
                    @php
                        $parts = explode('-', $yearMonth);
                        $label = \Carbon\Carbon::create($parts[0], $parts[1])->locale('id')->isoFormat('MMMM YYYY');
                        $pct   = round(($data['total'] / $maxRevenue) * 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div>
                                <p class="text-[13px] font-semibold text-gray-800">{{ $label }}</p>
                                <p class="text-[11px] text-gray-400">{{ $data['count'] }} booking</p>
                            </div>
                            <p class="text-[13px] font-bold text-gray-800">
                                Rp {{ number_format($data['total'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all"
                                style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════
             STATUS PEMBAYARAN
        ══════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-[15px] font-semibold text-gray-900">Status Pembayaran</h3>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div class="divide-y divide-gray-50">
                <div class="px-5 py-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-[14px] font-medium text-green-700">Lunas</span>
                    </div>
                    <span class="text-[14px] font-bold text-gray-800">{{ $statusCount['lunas'] }}</span>
                </div>
                <div class="px-5 py-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <span class="text-[14px] font-medium text-orange-700">Down Payment</span>
                    </div>
                    <span class="text-[14px] font-bold text-gray-800">{{ $statusCount['dp'] }}</span>
                </div>
                <div class="px-5 py-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-yellow-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <span class="text-[14px] font-medium text-yellow-700">Belum Bayar</span>
                    </div>
                    <span class="text-[14px] font-bold text-gray-800">{{ $statusCount['belum_bayar'] }}</span>
                </div>
            </div>
            <div class="px-5 py-4 bg-blue-50/50 border-t border-blue-100">
                <p class="text-[12px] font-medium text-blue-700 mb-1">Tingkat Pembayaran</p>
                <p class="text-[26px] font-bold text-blue-700 leading-none">{{ $collectionRate }}%</p>
                <p class="text-[11px] text-blue-500 mt-1">Dari total {{ $bookings->count() }} booking</p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             REVENUE PER JENIS LAYANAN
        ══════════════════════════════════════════════ --}}
        @if($revenueByService->count() > 0)
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-[15px] font-semibold text-gray-900">Revenue per Jenis Layanan</h3>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            @php $totalRevForPct = $revenueByService->sum('total') ?: 1; @endphp
            <div class="divide-y divide-gray-50">
                @foreach($revenueByService as $serviceName => $data)
                    @php $pct = round(($data['total'] / $totalRevForPct) * 100, 1); @endphp
                    <div class="px-5 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-[14px] font-semibold text-gray-900">{{ $serviceName }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    {{ $data['count'] }} booking &nbsp;·&nbsp;
                                    Rata-rata: Rp {{ number_format($data['average'], 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-[14px] font-bold text-gray-900">Rp {{ number_format($data['total'], 0, ',', '.') }}</p>
                                <p class="text-[11px] text-purple-600 font-semibold">{{ $pct }}%</p>
                            </div>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════
             STATISTIK RINGKAS
        ══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-center">
                <div class="w-10 h-10 mx-auto rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-[20px] font-bold text-gray-900">Rp {{ number_format(round($avgRevenuePerMonth), 0, ',', '.') }}</p>
                <p class="text-[12px] text-gray-500 mt-1">Rata-rata Revenue per Bulan</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-center">
                <div class="w-10 h-10 mx-auto rounded-2xl bg-green-50 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-[20px] font-bold text-gray-900">Rp {{ number_format(round($avgRevenuePerBooking), 0, ',', '.') }}</p>
                <p class="text-[12px] text-gray-500 mt-1">Rata-rata Revenue per Booking</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-center">
                <div class="w-10 h-10 mx-auto rounded-2xl bg-purple-50 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <p class="text-[20px] font-bold text-gray-900">{{ $collectionRate }}%</p>
                <p class="text-[12px] text-gray-500 mt-1">Collection Rate</p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             DETAIL TRANSAKSI BOOKING
        ══════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-[15px] font-semibold text-gray-900">Detail Transaksi Booking</h3>
                <span class="text-[12px] text-gray-500">{{ $bookings->count() }} transaksi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-[13px]">
                    <thead>
                        <tr class="bg-gray-50 text-left text-gray-500 text-[11px] font-semibold tracking-wide border-b border-gray-100">
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Klien</th>
                            <th class="px-5 py-3">Layanan</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Subtotal</th>
                            <th class="px-5 py-3 text-right">Biaya</th>
                            <th class="px-5 py-3 text-right">Diskon</th>
                            <th class="px-5 py-3 text-right">PPN</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-right">Diterima</th>
                            <th class="px-5 py-3 text-right">Sisa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($bookings as $booking)
                            @php $tgl = $booking->booking_date ?? $booking->start_date; @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $tgl ? \Carbon\Carbon::parse($tgl)->format('Y-m-d') : '-' }}
                                </td>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $booking->client_name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $booking->serviceType?->name ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $ps  = $booking->payment_status;
                                        $cls = match($ps) {
                                            'Lunas'        => 'bg-green-100 text-green-700',
                                            'Down Payment' => 'bg-orange-100 text-orange-700',
                                            default        => 'bg-yellow-100 text-yellow-700',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $cls }}">
                                        {{ $ps }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right text-gray-700">
                                    Rp {{ number_format($booking->subtotal ?? ($booking->unit_price * $booking->quantity), 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-right text-gray-500">Rp 0</td>
                                <td class="px-5 py-3 text-right text-red-500">
                                    Rp {{ number_format($booking->discount_amount ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-right text-gray-500">Rp 0</td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($booking->total, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-right text-emerald-600 font-semibold">
                                    Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold {{ $booking->remaining > 0 ? 'text-red-500' : 'text-gray-400' }}">
                                    Rp {{ number_format($booking->remaining, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @endif {{-- end @if($bookings->count() > 0) --}}

    </div>{{-- end content --}}

    {{-- ══════════════════════════════════════════════
         MODAL TAMBAH PEMASUKAN
    ══════════════════════════════════════════════ --}}
    <div x-show="showIncomeModal" x-cloak x-transition.opacity class="fixed inset-0 z-50">
        <div @click="showIncomeModal = false" class="absolute inset-0 bg-black/40"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div @click.stop class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">
                <h3 class="text-[18px] font-bold text-gray-900 mb-5">Tambah Pemasukan Tambahan</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                        <input type="text" x-model="incomeForm.description" placeholder="Contoh: Jasa editing tambahan"
                            class="w-full h-[42px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Jumlah (Rp)</label>
                        <input type="number" x-model="incomeForm.amount" placeholder="0" min="1"
                            class="w-full h-[42px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal</label>
                        <input type="date" x-model="incomeForm.date"
                            class="w-full h-[42px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button @click="showIncomeModal = false"
                        class="flex-1 h-[40px] rounded-xl bg-gray-100 text-gray-700 font-semibold text-[14px] hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button @click="saveIncome()"
                        class="flex-1 h-[40px] rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] transition">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL TAMBAH PENGELUARAN
    ══════════════════════════════════════════════ --}}
    <div x-show="showExpenseModal" x-cloak x-transition.opacity class="fixed inset-0 z-50">
        <div @click="showExpenseModal = false" class="absolute inset-0 bg-black/40"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div @click.stop class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">
                <h3 class="text-[18px] font-bold text-gray-900 mb-5">Tambah Pengeluaran</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                        <input type="text" x-model="expenseForm.description" placeholder="Contoh: Beli memory card"
                            class="w-full h-[42px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Jumlah (Rp)</label>
                        <input type="number" x-model="expenseForm.amount" placeholder="0" min="1"
                            class="w-full h-[42px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal</label>
                        <input type="date" x-model="expenseForm.date"
                            class="w-full h-[42px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button @click="showExpenseModal = false"
                        class="flex-1 h-[40px] rounded-xl bg-gray-100 text-gray-700 font-semibold text-[14px] hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button @click="saveExpense()"
                        class="flex-1 h-[40px] rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-[14px] transition">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>{{-- end x-data --}}

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons()
    })

    function financialPage() {
        return {
            showIncomeModal:  false,
            showExpenseModal: false,
            incomeForm:  { description: '', amount: '', date: '' },
            expenseForm: { description: '', amount: '', date: '' },

            async saveIncome() {
                if (!this.incomeForm.description || !this.incomeForm.amount || !this.incomeForm.date) {
                    alert('Semua field wajib diisi.')
                    return
                }
                try {
                    const res = await fetch('{{ route("financial.income.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept':        'application/json',
                            'X-CSRF-TOKEN':  document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify(this.incomeForm)
                    })
                    const data = await res.json()
                    if (data.success) {
                        this.showIncomeModal = false
                        this.incomeForm = { description: '', amount: '', date: '' }
                        window.location.reload()
                    }
                } catch (e) { alert('Gagal menyimpan.') }
            },

            async saveExpense() {
                if (!this.expenseForm.description || !this.expenseForm.amount || !this.expenseForm.date) {
                    alert('Semua field wajib diisi.')
                    return
                }
                try {
                    const res = await fetch('{{ route("financial.expense.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept':        'application/json',
                            'X-CSRF-TOKEN':  document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify(this.expenseForm)
                    })
                    const data = await res.json()
                    if (data.success) {
                        this.showExpenseModal = false
                        this.expenseForm = { description: '', amount: '', date: '' }
                        window.location.reload()
                    }
                } catch (e) { alert('Gagal menyimpan.') }
            }
        }
    }

    async function deleteIncome(id) {
        if (!confirm('Hapus pemasukan ini?')) return
        const res = await fetch(`/financial/income/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept':       'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        const data = await res.json()
        if (data.success) window.location.reload()
    }

    async function deleteExpense(id) {
        if (!confirm('Hapus pengeluaran ini?')) return
        const res = await fetch(`/financial/expense/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept':       'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        const data = await res.json()
        if (data.success) window.location.reload()
    }
</script>
<style>
    [x-cloak] { display: none !important; }
</style>
</body>
</html>