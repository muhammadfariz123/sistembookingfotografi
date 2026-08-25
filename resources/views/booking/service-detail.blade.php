<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Paket — {{ $service->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/css/glightbox.min.css" />
    <style>
        .bg-brand { background-color: #f59e0b; }
        .text-brand { color: #f59e0b; }
        .border-brand { border-color: #f59e0b; }
    </style>
</head>

<body class="bg-gray-50 min-h-screen text-gray-800">

    <x-customer-navbar :owner="$owner" :companySetting="$companySetting" :ownerId="$ownerId" showHome="true" />

    <main class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            {{-- KOLOM KIRI --}}
            <div class="w-full lg:w-2/3 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">

                {{-- Label Kategori & Judul --}}
                <div class="mb-6">
                    <p class="text-brand font-bold text-xs tracking-widest uppercase mb-2">
                        {{ optional($service->category)->name ?? 'PAKET FOTO' }}
                    </p>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">{{ $service->name }}</h1>
                </div>

                <hr class="border-gray-200 mb-6">

                {{-- Deskripsi --}}
                <div class="mb-8">
                    <h3 class="font-bold text-gray-900 mb-4 text-lg">Deskripsi Paket</h3>
                    <ul class="space-y-3">
                        @foreach(explode("\n", $service->description) as $line)
                            @if(trim($line))
                                <li class="flex items-start gap-3 text-gray-600">
                                    <span class="text-green-500 font-bold mt-0.5">✓</span>
                                    <span>{{ $line }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                {{-- Keuntungan Booking --}}
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 mb-10 space-y-4">
                    <h3 class="font-bold text-gray-900 mb-2">Keuntungan Booking</h3>
                    <div class="flex items-start gap-3 text-gray-600 text-sm">
                        <span class="text-green-500 font-bold mt-0.5">✓</span>
                        <span>Fotografer profesional berpengalaman</span>
                    </div>
                    <div class="flex items-start gap-3 text-gray-600 text-sm">
                        <span class="text-green-500 font-bold mt-0.5">✓</span>
                        <span>Proses booking mudah & cepat secara online</span>
                    </div>
                    <div class="flex items-start gap-3 text-gray-600 text-sm">
                        <span class="text-green-500 font-bold mt-0.5">✓</span>
                        <span>Notifikasi & detail booking dikirim via Email</span>
                    </div>
                </div>

                {{-- Galeri Foto --}}
                <div>
                    <h3 class="font-bold text-gray-900 mb-4 text-lg border-b border-gray-200 pb-2">Contoh Hasil Foto</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                        @php 
                            // MENGAMBIL FOTO DARI RELASI KATEGORI
                            $galleries = optional($service->category)->galleries ?? collect();
                            $totalPhotos = $galleries->count(); 
                        @endphp          
       
                        @if($totalPhotos > 0)
                            @foreach($galleries->take(6) as $index => $gallery)
                                @if($index == 5 && $totalPhotos > 6)
                                    <a href= "{{ route('booking.service.gallery', ['ownerId' => $ownerId, 'serviceId' => $service->id]) }}" class="relative aspect-square bg-gray-200 rounded-xl overflow-hidden shadow-sm block group">
                                        <img src="{{ str_starts_with($gallery->image_path, 'http') ? $gallery->image_path : Storage::url($gallery->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 filter blur-[2px]" alt="Hasil Foto">
                                        <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center text-white transition duration-300 group-hover:bg-black/60">
                                            <span class="text-2xl font-bold mb-1">+{{ $totalPhotos - 5 }}</span>
                                            <span class="text-xs font-medium px-2 text-center">Lihat Selengkapnya</span>
                                        </div>
                                    </a>        
                                @else
                                    <a href="{{ str_starts_with($gallery->image_path, 'http') ? $gallery->image_path : Storage::url($gallery->image_path) }}" data-gallery="service-gallery" class="glightbox aspect-square bg-gray-200 rounded-xl overflow-hidden shadow-sm block group">
                                        <img src="{{ str_starts_with($gallery->image_path, 'http') ? $gallery->image_path : Storage::url($gallery->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="Hasil Foto">
                                    </a>
                                @endif
                            @endforeach
                        @else
                            <div class="col-span-full py-8 text-center bg-gray-50 border border-dashed border-gray-200 rounded-xl">
                                <p class="text-gray-400 text-sm">Belum ada foto galeri untuk kategori ini.</p>
                            </div>
                        @endif
                    </div>
                                        
                    @if($totalPhotos > 6)
                        <a href="{{ route('booking.service.gallery', ['ownerId' => $ownerId, 'serviceId' => $service->id]) }}" class="block w-full py-3.5 border-2 border-gray-200 text-center rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition">
                            Lihat Selengkapnya Hasil Foto
                        </a>
                    @endif
                </div>
            </div>

            {{-- KOLOM KANAN (Kotak Harga & Tombol Aksi) --}}
            <div class="w-full lg:w-1/3 sticky top-24">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <div class="mb-6">
                        <div class="inline-block bg-amber-100 text-brand font-bold text-xs px-3 py-1 rounded-full mb-3 uppercase">
                            {{ optional($service->category)->name ?? 'HARGA PAKET' }}
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 tracking-tight">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                    </div>

                    <div class="flex flex-col gap-3 mb-6">
                        <a href="{{ route('booking.public.form', ['ownerId' => $ownerId, 'serviceId' => $service->id]) }}" class="w-full py-3 text-center rounded-xl bg-brand text-white font-semibold text-sm hover:opacity-90 transition shadow-sm">
                            Booking Sekarang
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $companySetting?->company_phone ?? '') }}?text=Halo, saya ingin bertanya tentang paket {{ $service->name }}"
                            target="_blank"
                            class="flex items-center justify-center gap-2 w-full border-2 border-green-500 text-green-600 py-3.5 rounded-xl font-bold hover:bg-green-50 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            Tanya via WhatsApp
                        </a>
                    </div>

                    <div class="space-y-3 pt-4 border-t border-gray-100">
                        <div class="flex items-start gap-3 text-xs text-gray-500">
                            <svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Booking online 24 jam kapan saja</span>
                        </div>
                        <div class="flex items-start gap-3 text-xs text-gray-500">
                            <svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>Notifikasi booking dikirim otomatis via Email</span>
                        </div>
                        <div class="flex items-start gap-3 text-xs text-gray-500">
                            <svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Bisa reschedule sebelum sesi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center lg:text-left">
            <a href="{{ route('booking.services.all', $ownerId) }}" class="inline-flex items-center justify-center gap-2 border-2 border-gray-300 text-gray-700 px-8 py-3 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Lihat Semua Paket
            </a>
        </div>
    </main>

    <x-customer-footer :owner="$owner" :companySetting="$companySetting" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/js/glightbox.min.js"></script>
    <script>
        const lightbox = GLightbox({ loop: true });
    </script>
</body>
</html>