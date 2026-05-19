<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice DP — {{ $invoiceNumber }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 font-sans antialiased">

    {{-- TOPBAR --}}
    <div class="sticky top-0 z-50 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm">
        <h1 class="text-[16px] font-bold text-gray-900">Invoice DP Booking</h1>
        <div class="flex items-center gap-2">
            <button onclick="downloadPDF()"
                class="h-[38px] px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold flex items-center gap-2 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download PDF
            </button>
            <button onclick="window.print()"
                class="h-[38px] px-4 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-semibold flex items-center gap-2 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
            <a href="{{ route('booking.public.show', $ownerId) }}"
                class="h-[38px] w-[38px] rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- NOTIF DP --}}
    <div class="max-w-3xl mx-auto px-4 pt-6">
        <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 mb-5 flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-blue-700">
                Setelah booking berhasil dikirim, customer wajib melakukan pembayaran minimal
                <strong>DP {{ $dpPercent }}%</strong> dari total layanan yang dipilih.
            </p>
        </div>
    </div>

    {{-- INVOICE PAPER --}}
    <div class="max-w-3xl mx-auto px-4 pb-12" id="invoice-content">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

            {{-- HEADER INVOICE --}}
            <div class="flex items-start justify-between mb-8">
                <div>
                    @if($companySetting?->company_logo)
                        <img src="{{ Storage::url($companySetting->company_logo) }}"
                             class="h-14 w-auto object-contain mb-2" alt="Logo">
                    @else
                        <div class="w-16 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            </svg>
                        </div>
                    @endif
                    <p class="font-bold text-blue-600 text-[18px]">
                        {{ $companySetting?->company_name ?? $owner->name }}
                    </p>
                    @if($companySetting?->company_address)
                        <p class="text-sm text-gray-500 mt-0.5">{{ $companySetting->company_address }}</p>
                    @endif
                    @if($companySetting?->company_phone)
                        <p class="text-sm text-gray-500">Tel: {{ $companySetting->company_phone }}</p>
                    @endif
                    @if($companySetting?->company_email)
                        <p class="text-sm text-gray-500">Email: {{ $companySetting->company_email }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900 uppercase tracking-wide">INVOICE DP</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $invoiceNumber }}</p>
                </div>
            </div>

            <hr class="border-gray-200 mb-6">

            {{-- BILL TO + TANGGAL --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-2">Bill To:</p>
                    <p class="font-bold text-gray-900 text-[16px]">{{ $booking->client_name }}</p>
                    <p class="text-sm text-gray-600 mt-0.5">{{ $booking->client_contact }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $booking->client_address }}</p>
                </div>
                <div class="sm:text-right space-y-1.5">
                    <div class="flex sm:justify-end gap-4">
                        <span class="text-sm text-gray-500 w-36">Tanggal Invoice</span>
                        <span class="text-sm font-medium text-gray-800">
                            {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}
                        </span>
                    </div>
                    <div class="flex sm:justify-end gap-4">
                        <span class="text-sm text-gray-500 w-36">Jatuh Tempo</span>
                        <span class="text-sm font-medium text-gray-800">
                            {{ now()->addDays(7)->locale('id')->isoFormat('D MMMM YYYY') }}
                        </span>
                    </div>
                    <div class="flex sm:justify-end gap-4">
                        <span class="text-sm text-gray-500 w-36">Tanggal Layanan</span>
                        @php $tgl = $booking->booking_date ?? $booking->start_date; @endphp
                        <span class="text-sm font-medium text-gray-800">
                            {{ $tgl ? \Carbon\Carbon::parse($tgl)->locale('id')->isoFormat('D MMMM YYYY') : '-' }}
                        </span>
                    </div>
                    <div class="flex sm:justify-end gap-4">
                        <span class="text-sm text-gray-500 w-36">Status</span>
                        <span class="text-sm font-semibold text-yellow-600">
                            {{ $booking->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- TABEL LAYANAN --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden mb-6">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-semibold tracking-wide uppercase">
                            <th class="px-4 py-3 text-left">Deskripsi Layanan</th>
                            <th class="px-4 py-3 text-center">Qty</th>
                            <th class="px-4 py-3 text-right">Harga per Unit</th>
                            <th class="px-4 py-3 text-center">Tanggal & Waktu</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-gray-200">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-gray-900">
                                    {{ $booking->serviceType?->name ?? '-' }}
                                </p>
                                @if($booking->serviceType?->description)
                                    <p class="text-xs text-gray-500 mt-1 whitespace-pre-line">
                                        {{ $booking->serviceType->description }}
                                    </p>
                                @endif
                                @if($booking->notes)
                                    <p class="text-xs text-blue-500 mt-1">{{ $booking->notes }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center text-gray-700">
                                {{ $booking->quantity }}
                            </td>
                            <td class="px-4 py-4 text-right text-gray-700">
                                Rp {{ number_format($booking->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-center text-gray-600">
                                {{ $tgl ? \Carbon\Carbon::parse($tgl)->locale('id')->isoFormat('D MMM YYYY') : '-' }}
                                @if($booking->booking_time)
                                    <br><span class="text-xs text-gray-400">
                                        {{ substr($booking->booking_time, 0, 5) }}
                                    </span>
                                @endif
                            </td>
                            {{-- ── Jumlah = unit_price × quantity (Rumus 3.1 Subtotal) ── --}}
                            <td class="px-4 py-4 text-right font-semibold text-gray-900">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- SUMMARY KEUANGAN --}}
            <div class="flex justify-end mb-6">
                <div class="w-full sm:w-80 space-y-2">

                    {{-- Subtotal (Rumus 3.1) --}}
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal
                            <span class="text-xs text-gray-400">({{ $booking->quantity }} × Rp {{ number_format($booking->unit_price, 0, ',', '.') }})</span>:
                        </span>
                        <span class="font-medium text-gray-800">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Diskon jika ada (Rumus 3.2) --}}
                    @if($discountAmount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Diskon ({{ number_format($discountPercent, 0) }}%)</span>
                            <span class="font-medium text-red-500">
                                - Rp {{ number_format($discountAmount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    {{-- Total (Rumus 3.3) --}}
                    <div class="flex justify-between text-sm border-t border-gray-200 pt-2">
                        <span class="font-medium text-gray-700">Total:</span>
                        <span class="font-semibold text-gray-900">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- DP 30% --}}
                    <div class="flex justify-between text-sm">
                        <span class="text-blue-600 font-medium">DP {{ $dpPercent }}%:</span>
                        <span class="font-bold text-blue-600">
                            Rp {{ number_format($dpAmount, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Sisa setelah DP --}}
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Sisa setelah DP:</span>
                        <span class="font-medium text-red-500">
                            Rp {{ number_format($sisaAfterDp, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Total invoice --}}
                    <div class="border-t border-gray-200 pt-2 flex justify-between">
                        <span class="font-bold text-gray-900">Total invoice ini:</span>
                        <span class="font-bold text-gray-900 text-[16px]">
                            Rp {{ number_format($dpAmount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- INFORMASI PEMBAYARAN --}}
            <div class="border border-gray-200 rounded-xl p-5">
                <p class="font-semibold text-gray-800 mb-4">Informasi Pembayaran</p>

                {{-- Jumlah DP --}}
                <div class="border-2 border-amber-400 rounded-xl px-5 py-4 mb-4 text-center">
                    <p class="text-sm font-semibold text-amber-600 mb-1">
                        Jumlah DP yang harus ditransfer
                    </p>
                    <p class="text-3xl font-bold text-amber-600">
                        Rp {{ number_format($dpAmount, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Info bank --}}
                @if($companySetting?->bank_name || $companySetting?->bank_account)
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2">Transfer Bank:</p>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 inline-block">
                            @if($companySetting->bank_name)
                                <p class="text-sm text-gray-700">
                                    <b>Bank:</b> {{ $companySetting->bank_name }}
                                </p>
                            @endif
                            @if($companySetting->bank_account)
                                <p class="text-sm text-gray-700">
                                    <b>No. Rekening:</b> {{ $companySetting->bank_account }}
                                </p>
                            @endif
                            @if($companySetting->bank_holder)
                                <p class="text-sm text-gray-700">
                                    <b>A.n:</b> {{ $companySetting->bank_holder }}
                                </p>
                            @endif
                        </div>

                        {{-- Bank kedua jika ada --}}
                        @if($companySetting?->bank_name_2)
                            <div class="bg-gray-50 rounded-xl px-4 py-3 inline-block mt-2">
                                <p class="text-sm text-gray-700">
                                    <b>Bank:</b> {{ $companySetting->bank_name_2 }}
                                </p>
                                @if($companySetting->bank_account_2)
                                    <p class="text-sm text-gray-700">
                                        <b>No. Rekening:</b> {{ $companySetting->bank_account_2 }}
                                    </p>
                                @endif
                                @if($companySetting->bank_holder_2)
                                    <p class="text-sm text-gray-700">
                                        <b>A.n:</b> {{ $companySetting->bank_holder_2 }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Instruksi pembayaran --}}
                @if($companySetting?->payment_instruction)
                    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                        <p class="text-sm font-semibold text-amber-700 mb-1">Instruksi Pembayaran:</p>
                        <p class="text-sm text-amber-600">{{ $companySetting->payment_instruction }}</p>
                    </div>
                @else
                    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                        <p class="text-sm font-semibold text-amber-700 mb-1">Instruksi Pembayaran:</p>
                        <p class="text-sm text-amber-600">
                            Silakan transfer ke rekening di atas dan kirimkan bukti transfer untuk konfirmasi pembayaran.
                        </p>
                    </div>
                @endif
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                Invoice ini dibuat secara otomatis oleh sistem
                {{ $companySetting?->company_name ?? $owner->name }}
            </p>
        </div>
    </div>

    <script>
        async function downloadPDF() {
            const { default: html2pdf } = await import('https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js')
            const el = document.getElementById('invoice-content')
            html2pdf().set({
                margin:      [10, 10],
                filename:    '{{ $invoiceNumber }}.pdf',
                image:       { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(el).save()
        }
    </script>

    <style>
        @media print {
            .sticky { display: none !important; }
            body { background: white !important; }
        }
    </style>
</body>
</html>