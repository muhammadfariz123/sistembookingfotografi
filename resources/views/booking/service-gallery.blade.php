<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto {{ optional($service->category)->name ?? $service->name }} — {{ $companySetting?->company_name ?? $owner->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/css/glightbox.min.css" />
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand { background-color: #f59e0b; }
        .text-brand { color: #f59e0b; }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    {{-- PANGGIL KOMPONEN NAVBAR --}}
    <x-customer-navbar :owner="$owner" :companySetting="$companySetting" :ownerId="$ownerId" showHome="true" />

    {{-- KONTEN UTAMA --}}
    <main class="flex-grow max-w-6xl mx-auto w-full py-10 px-4">
        
        {{-- TOMBOL KEMBALI --}}
        <a href="{{ route('booking.service.detail', ['ownerId' => $ownerId, 'serviceId' => $service->id]) }}" 
            class="inline-flex items-center text-gray-500 hover:text-brand transition mb-8 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Detail Paket
        </a>

        {{-- Header Halaman --}}
        <div class="mb-10">
            <p class="text-brand font-bold text-xs tracking-widest uppercase mb-2">GALERI HASIL FOTO</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-3">{{ optional($service->category)->name ?? $service->name }}</h1>
            <p class="text-gray-500 text-sm md:text-base">
                Berikut adalah keseluruhan portofolio dan hasil foto untuk kategori acara ini. Klik pada gambar untuk memperbesar.
            </p>
        </div>

        @php
            // MENGAMBIL FOTO DARI RELASI KATEGORI
            $galleries = optional($service->category)->galleries ?? collect();
        @endphp

        {{-- GRID FULL GALLERY --}}
        @if($galleries->isEmpty())
            <div class="text-center py-16 bg-white border border-dashed border-gray-300 rounded-2xl">
                <p class="text-gray-500">Belum ada foto galeri untuk kategori ini.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach($galleries as $gallery)
                    <a href="{{ Storage::url($gallery->image_path) }}" 
                       class="glightbox aspect-square bg-gray-200 rounded-2xl overflow-hidden shadow-sm block group relative">
                        <img src="{{ Storage::url($gallery->image_path) }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-700" 
                             alt="Hasil Foto">
                        
                        {{-- Icon Zoom on Hover --}}
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <div class="bg-white/90 p-3 rounded-full text-gray-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </main>

    {{-- PANGGIL KOMPONEN FOOTER --}}
    <x-customer-footer :owner="$owner" :companySetting="$companySetting" />

    {{-- SCRIPT LIGHTBOX UNTUK ZOOM FOTO --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/js/glightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const lightbox = GLightbox({ 
                loop: true,
                zoomable: true,
                touchNavigation: true
            });
        });
    </script>
</body>

</html>