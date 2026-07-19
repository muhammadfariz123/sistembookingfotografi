<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $companySetting?->company_name ?? $owner->name }} — Studio Foto Profesional</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Menggunakan warna hex spesifik agar identik dengan gambar */
        .bg-brand {
            background-color: #f59e0b;
        }

        .text-brand {
            color: #f59e0b;
        }

        .border-brand {
            border-color: #f59e0b;
        }
    </style>
</head>

<body class="bg-white text-gray-800 antialiased">

    {{-- PANGGIL KOMPONEN NAVBAR --}}
    <x-customer-navbar :owner="$owner" :companySetting="$companySetting" :ownerId="$ownerId" />

    {{-- HERO SECTION --}}
    <div class="bg-brand text-white text-center py-24 px-4">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-[52px] font-bold mb-6 leading-tight">
                Abadikan Momen<br>Terbaikmu
            </h1>
            <p class="text-lg md:text-xl mb-10 opacity-95">
                Studio foto profesional untuk semua kebutuhan fotografimu
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#paket"
                    class="bg-white text-brand px-8 py-3.5 rounded-lg font-semibold w-full sm:w-auto hover:bg-gray-50 transition">
                    Booking Sekarang
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $companySetting?->company_phone ?? '') }}"
                    target="_blank"
                    class="border border-white text-white px-8 py-3.5 rounded-lg font-semibold w-full sm:w-auto hover:bg-white/10 transition">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>

    {{-- LAYANAN KAMI (DINAMIS DARI DATABASE) --}}
    <div id="paket" class="bg-white py-20 px-4 md:px-12">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-brand font-bold text-xs tracking-[0.2em] uppercase mb-3">LAYANAN KAMI</p>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Pilih Paket Foto</h2>
            <p class="text-gray-500 text-sm md:text-base">Temukan paket yang paling sesuai dengan kebutuhan dan momen
                spesialmu.</p>
        </div>

        @if($services->isEmpty())
            <div class="text-center py-10 text-gray-400 border border-dashed border-gray-300 rounded-xl max-w-3xl mx-auto">
                Belum ada paket layanan yang ditambahkan oleh admin.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                {{-- BATASI HANYA MENAMPILKAN MAKSIMAL 3 PAKET --}}
                @foreach($services->take(3) as $service)
                    {{-- KARTU PAKET --}}
                    <div
                        class="border border-gray-200 rounded-[20px] p-6 sm:p-8 hover:shadow-lg transition duration-300 bg-white flex flex-col h-full">
                        <div class="flex-grow">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $service->name }}</h3>

                            {{-- Deskripsi --}}
                            @if($service->description)
                                <p class="text-sm text-gray-500 mb-4 whitespace-pre-line">{{ $service->description }}</p>
                            @endif
                        </div>

                        <div class="mt-auto">
                            <h4 class="text-2xl font-bold text-brand mb-6">
                                Rp {{ number_format($service->price, 0, ',', '.') }}
                            </h4>
                            <div class="flex flex-col gap-3">
                                <a href="{{ route('booking.service.detail', ['ownerId' => $ownerId, 'serviceId' => $service->id]) }}"
                                    class="w-full py-3 text-center rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">
                                    Lihat Detail
                                </a>
                                {{-- Tombol Booking --}}
                                <a href="{{ route('booking.service.detail', ['ownerId' => $ownerId, 'serviceId' => $service->id]) }}"
                                    class="w-full py-3 text-center rounded-xl bg-brand text-white font-semibold text-sm hover:opacity-90 transition shadow-sm">
                                    Booking Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- TOMBOL LIHAT SEMUA PAKET: HANYA MUNCUL JIKA JUMLAH PAKET LEBIH DARI 3 --}}
            @if($services->count() > 3)
                <div class="text-center mt-12">
                    {{-- Ganti tanda # dengan route ke halaman daftar semua paket jika sudah ada --}}
                    <a href="{{ route('booking.services.all', $ownerId) }}"
                        class="inline-flex items-center justify-center gap-2 border border-gray-200 text-gray-600 px-6 py-3 rounded-full text-sm font-semibold hover:bg-gray-50 transition">
                        Lihat Semua Paket
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @endif
        @endif
    </div>


    {{-- PANGGIL KOMPONEN FOOTER --}}
    <x-customer-footer :owner="$owner" :companySetting="$companySetting" />
</body>

</html>