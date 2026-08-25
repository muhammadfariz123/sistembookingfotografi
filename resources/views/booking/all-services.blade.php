<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Paket — {{ $companySetting?->company_name ?? $owner->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand { background-color: #f59e0b; }
        .text-brand { color: #f59e0b; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <x-customer-navbar :owner="$owner" :companySetting="$companySetting" :ownerId="$ownerId" showHome="true" />

    <main class="flex-grow max-w-6xl mx-auto w-full py-16 px-4">
        <div class="mb-10 text-center flex flex-col items-center">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">Semua Layanan Paket Foto</h1>
            <p class="text-gray-500 text-sm md:text-base max-w-2xl mx-auto">
                Jelajahi seluruh pilihan paket foto yang kami tawarkan. Temukan yang paling cocok untuk mengabadikan momen spesial Anda.
            </p>
        </div>

        @if($services->isEmpty())
            <div class="text-center py-16 bg-white border border-dashed border-gray-300 rounded-2xl">
                <p class="text-gray-500">Belum ada paket layanan yang tersedia.</p>
            </div>
        @else
            @php
                // Mengelompokkan service berdasarkan nama kategori dari relasi
                $groupedServices = $services->groupBy(function($item) {
                    return $item->category ? trim($item->category->name) : 'Lain-lain';
                });
            @endphp

            @foreach($groupedServices as $categoryName => $catServices)
                <div class="mb-16">
                    {{-- Judul Kategori --}}
                    <div class="flex items-center gap-4 mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900 uppercase tracking-wide">{{ $categoryName }}</h2>
                        <div class="h-px bg-gray-200 flex-grow"></div>
                    </div>

                    {{-- Grid Paket dalam Kategori ini --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($catServices as $service)
                            <div class="border border-gray-200 rounded-[20px] p-6 sm:p-8 hover:shadow-lg transition duration-300 bg-white flex flex-col h-full">
                                <div class="flex-grow">
                                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $service->name }}</h3>
                                    @if($service->description)
                                        <p class="text-sm text-gray-500 mb-4 whitespace-pre-line line-clamp-4">{{ $service->description }}</p>
                                    @endif
                                </div>
                                <div class="mt-auto pt-4 border-t border-gray-50">
                                    <h4 class="text-2xl font-bold text-brand mb-6">
                                        Rp {{ number_format($service->price, 0, ',', '.') }}
                                    </h4>
                                    <div class="flex flex-col gap-3">
                                        <a href="{{ route('booking.service.detail', ['ownerId' => $ownerId, 'serviceId' => $service->id]) }}"
                                            class="w-full py-3 text-center rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">
                                            Lihat Detail
                                        </a>
                                        <a href="{{ route('booking.public.form', ['ownerId' => $ownerId, 'serviceId' => $service->id]) }}"
                                            class="w-full py-3 text-center rounded-xl bg-brand text-white font-semibold text-sm hover:opacity-90 transition shadow-sm">
                                            Booking Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

        {{-- SECTION KONSULTASI --}}
        <div class="pt-16 pb-12 text-center flex flex-col items-center justify-center">
            <p class="text-gray-600 mb-6 text-sm md:text-base font-medium">
                Tidak yakin pilih paket mana? Konsultasikan dulu dengan kami.
            </p>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $companySetting?->company_phone ?? '') }}" target="_blank"
                class="inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-white px-8 py-3.5 rounded-xl font-bold shadow-md transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
                Konsultasi via WhatsApp
            </a>
        </div>
    </main>

    <x-customer-footer :owner="$owner" :companySetting="$companySetting" />
</body>
</html>