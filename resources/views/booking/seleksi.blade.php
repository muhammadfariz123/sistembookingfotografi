{{-- resources/views/booking/seleksi.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Foto — {{ $companyName }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-[#fafafa] min-h-screen text-gray-900" x-data="photoSelection()">

    {{-- TOP NAVBAR --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-[72px] flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ url('/cek-booking/result?booking_code=' . $bookingCode) }}"
                    class="text-gray-400 hover:text-gray-900 transition flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </a>

                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-orange-400 text-white font-extrabold flex items-center justify-center text-lg shadow-sm">
                        {{ strtoupper(substr($companyName, 0, 1)) }}
                    </div>
                    <div class="leading-tight">
                        <h1 class="font-bold text-[15px] text-gray-900">{{ $companyName }}</h1>
                        <p class="text-[12px] text-gray-400">{{ $booking->client_name }} · {{ $bookingCode }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-5">
                <div class="text-center hidden sm:block leading-tight">
                    <p class="text-[11px] text-gray-400 font-medium">Dipilih</p>
                    <p class="text-[12px] text-gray-500 font-medium">
                        <span class="font-extrabold text-orange-400 text-[18px]" x-text="selectedPhotos.length"></span>
                        dari <span x-text="maxLimit"></span>
                    </p>
                </div>
                <button type="button" @click="openSubmitModal()" :disabled="selectedPhotos.length === 0"
                    class="bg-[#fcd085] text-white font-bold px-6 py-2.5 rounded-xl flex items-center gap-2 hover:bg-[#fbbf59] transition disabled:opacity-60 disabled:cursor-not-allowed shadow-sm">
                    Kirim <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-24">

        {{-- INFO BANNER --}}
        <div class="bg-[#f3f8ff] border border-[#dbe8fe] rounded-xl p-4 flex items-start gap-3 mb-6 shadow-sm">
            <div class="mt-0.5 text-blue-500 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-[13px] text-blue-800 leading-relaxed flex-1">
                <p>Ketuk foto untuk memilih. Tekan ikon 🔍 untuk lihat detail. Maksimal <b
                        x-text="maxLimit + ' foto'"></b> dari total <span x-text="photos.length"></span> foto asli dari
                    Google Drive.</p>
                @if($booking->link_original)
                    <div class="mt-2">
                        <a href="{{ $booking->link_original }}" target="_blank"
                            class="inline-flex items-center gap-1.5 bg-white border border-blue-200 text-orange-600 font-bold px-3 py-1.5 rounded-lg shadow-xs hover:bg-orange-50 transition">
                            📁 Buka Folder Google Drive Asli ↗
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- ERROR ALERT JIKA API GAGAL --}}
        @if(isset($apiError))
            <div
                class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 text-[13px] flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                <div>
                    <span class="font-bold">Koneksi Google Drive Bermasalah:</span> {{ $apiError }}
                </div>
            </div>
        @endif

        {{-- FILTER & SORTING --}}
        <div class="flex items-center gap-3 mb-8" x-show="photos.length > 0" x-cloak>
            <span class="text-[13px] font-medium text-gray-600">Urutan:</span>
            <button @click="sortOrder = 'asc'; sortPhotos()"
                class="px-4 py-1.5 rounded-lg text-[13px] font-bold transition"
                :class="sortOrder === 'asc' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'">
                A &rarr; Z
            </button>
            <button @click="sortOrder = 'desc'; sortPhotos()"
                class="px-4 py-1.5 rounded-lg text-[13px] font-bold transition"
                :class="sortOrder === 'desc' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'">
                Z &rarr; A
            </button>
        </div>

        {{-- GALLERY SECTION --}}
        <div>
            <div class="flex items-center gap-3 mb-5">
                <h2 class="text-[15px] font-bold text-gray-900 flex items-center gap-2">
                    📁 Foto dari Google Drive
                    <span class="bg-gray-100 text-gray-500 text-[11px] px-2.5 py-0.5 rounded-full font-medium"
                        x-text="photos.length + ' foto'"></span>
                </h2>
            </div>

            {{-- JIKA FOTO KOSONG --}}
            <div x-show="photos.length === 0" x-cloak
                class="text-center py-16 bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z">
                        </path>
                    </svg>
                </div>
                <p class="font-bold text-gray-900">Belum ada foto atau folder kosong</p>
                <p class="text-[13px] text-gray-500 mt-1 max-w-md mx-auto">Pastikan folder Google Drive berisi file foto
                    (.jpg, .png) dan tautan sudah diatur publik.</p>
            </div>

            {{-- GRID FOTO ASLI (MENGGUNAKAN PHOTO.FILENAME) --}}
            <div x-show="photos.length > 0" x-cloak
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5">
                <template x-for="(photo, index) in photos" :key="photo.id">
                    <div class="flex flex-col group cursor-pointer" @click="toggleSelection(photo.filename)">

                        {{-- Image Wrapper --}}
                        <div class="aspect-[4/3] bg-gray-100 rounded-xl overflow-hidden relative border transition-all duration-200"
                            :class="isSelected(photo.filename) ? 'ring-4 ring-orange-400 border-transparent shadow-md' : 'border-gray-200 hover:border-gray-300 shadow-sm'">

                            {{-- Gambar Asli dari Google Drive --}}
                            <img :src="photo.url" :alt="photo.filename" loading="lazy"
                                class="w-full h-full object-cover">

                            {{-- Indikator Centang --}}
                            <div class="absolute top-2 right-2 w-6 h-6 rounded-full border-2 shadow-sm transition-colors duration-200 flex items-center justify-center"
                                :class="isSelected(photo.filename) ? 'bg-orange-500 border-white' : 'bg-white/90 border-transparent'">
                                <svg x-show="isSelected(photo.filename)" class="w-3.5 h-3.5 text-white" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>

                            {{-- Tombol Zoom (Membuka Modal Lightbox) --}}
                            <div class="absolute bottom-2 right-2 w-8 h-8 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-md"
                                @click.stop="openModal(index)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7">
                                    </path>
                                </svg>
                            </div>
                        </div>

                        {{-- Nama File Foto --}}
                        <p class="text-[12px] font-medium text-gray-600 mt-2 truncate px-0.5" x-text="photo.filename">
                        </p>
                    </div>
                </template>
            </div>
        </div>

    </main>

    {{-- LIGHTBOX MODAL (Pratinjau Gambar) --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 bg-black/90 flex flex-col justify-between p-4 sm:p-6"
        @keydown.escape.window="closeModal()">
        <div class="flex items-center justify-between text-white max-w-6xl mx-auto w-full">
            <div class="text-sm font-medium tracking-wide">
                <span x-text="currentIndex + 1"></span> / <span x-text="photos.length"></span>
                <span class="ml-2 font-bold text-gray-200" x-text="currentPhoto ? currentPhoto.filename : ''"></span>
            </div>

            <div class="flex items-center gap-4">
                <button type="button" @click="toggleSelection(currentPhoto.filename)"
                    class="px-4 py-2 rounded-xl font-bold text-sm flex items-center gap-2 transition shadow-sm"
                    :class="isSelected(currentPhoto?.filename) ? 'bg-orange-500 text-white' : 'bg-white/20 hover:bg-white/30 text-white'">
                    <svg x-show="isSelected(currentPhoto?.filename)" class="w-4 h-4 text-white" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span x-text="isSelected(currentPhoto?.filename) ? '✓ Dipilih' : 'Pilih Foto'"></span>
                </button>

                <template x-if="currentPhoto">
                    <a :href="'https://drive.google.com/file/d/' + currentPhoto.id + '/view'" target="_blank"
                        class="text-sm text-gray-300 hover:text-white underline items-center gap-1 flex">
                        Buka di Drive ↗
                    </a>
                </template>

                <button @click="closeModal()"
                    class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="relative flex-1 flex items-center justify-center max-w-5xl mx-auto w-full my-4">
            <button @click="prevPhoto()"
                class="absolute left-2 sm:-left-12 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <template x-if="currentPhoto">
                <img :src="currentPhoto.url" :alt="currentPhoto.filename"
                    class="max-h-[75vh] max-w-full object-contain rounded-lg shadow-2xl">
            </template>

            <button @click="nextPhoto()"
                class="absolute right-2 sm:-right-12 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        <div class="text-center text-xs text-gray-400 pb-2">
            Gunakan tombol panah keyboard (&larr; &rarr;) untuk navigasi foto.
        </div>
    </div>

    {{-- SUBMIT CONFIRMATION MODAL --}}
    <div x-show="submitModalOpen" x-cloak
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 relative animate-in fade-in zoom-in duration-150">

            <button @click="submitModalOpen = false"
                class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <h3 class="text-[17px] font-extrabold text-gray-900 mb-1">Konfirmasi Pilihan</h3>
            <p class="text-[13px] text-gray-500 mb-5">
                <span class="font-bold text-orange-500" x-text="selectedPhotos.length"></span> foto dipilih <span
                    class="text-gray-400">dari <span x-text="maxLimit"></span></span>
            </p>

            <div class="mb-6">
                <label class="block text-[13px] font-bold text-gray-700 mb-2">Catatan untuk admin <span
                        class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea x-model="clientNotes" rows="3"
                    placeholder="mis: foto no.3 tolong lebih cerah, foto kedua kurang tajam..."
                    class="w-full border border-gray-200 rounded-2xl p-3.5 text-[13px] focus:ring-2 focus:ring-orange-400 focus:outline-none transition resize-none text-gray-800 placeholder-gray-400"></textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="submitModalOpen = false"
                    class="flex-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold py-3.5 rounded-2xl text-[14px] transition shadow-xs">
                    Batal
                </button>
                <button type="button" @click="confirmSubmit()"
                    class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3.5 rounded-2xl text-[14px] transition shadow-sm flex items-center justify-center gap-1.5">
                    Kirim Pilihan ✓
                </button>
            </div>
        </div>
    </div>

    {{-- FLOATING BOTTOM BAR (Mobile) --}}
    <div
        class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 sm:hidden z-40 flex items-center justify-between shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <div class="leading-tight">
            <p class="text-[11px] text-gray-400 font-medium">Dipilih</p>
            <p class="text-[12px] text-gray-500 font-medium">
                <span class="font-extrabold text-orange-400 text-[18px]" x-text="selectedPhotos.length"></span> / <span
                    x-text="maxLimit"></span>
            </p>
        </div>
        <button type="button" @click="openSubmitModal()" :disabled="selectedPhotos.length === 0"
            class="bg-[#fcd085] text-white font-bold px-6 py-2.5 rounded-xl flex items-center gap-2 hover:bg-[#fbbf59] transition disabled:opacity-60 disabled:cursor-not-allowed">
            Kirim <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                </path>
            </svg>
        </button>
    </div>

    <script>
        function photoSelection() {
            return {
                maxLimit: {{ $limitFoto }},
                selectedPhotos: @json($previousSelected), // Berisi array nama file (string)
                sortOrder: 'asc',
                photos: @json($photos),

                modalOpen: false,
                currentIndex: 0,

                submitModalOpen: false,
                clientNotes: '',

                get currentPhoto() {
                    return this.photos[this.currentIndex] || null;
                },

                openModal(index) {
                    this.currentIndex = index;
                    this.modalOpen = true;
                },

                closeModal() {
                    this.modalOpen = false;
                },

                nextPhoto() {
                    if (this.currentIndex < this.photos.length - 1) {
                        this.currentIndex++;
                    } else {
                        this.currentIndex = 0;
                    }
                },

                prevPhoto() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    } else {
                        this.currentIndex = this.photos.length - 1;
                    }
                },

                sortPhotos() {
                    this.photos.sort((a, b) => {
                        if (this.sortOrder === 'asc') {
                            return a.filename.localeCompare(b.filename);
                        } else {
                            return b.filename.localeCompare(a.filename);
                        }
                    });
                },

                isSelected(filename) {
                    return this.selectedPhotos.includes(filename);
                },

                toggleSelection(filename) {
                    if (this.isSelected(filename)) {
                        this.selectedPhotos = this.selectedPhotos.filter(p => p !== filename);
                    } else {
                        if (this.selectedPhotos.length >= this.maxLimit) {
                            alert('Batas maksimal pemilihan foto adalah ' + this.maxLimit + ' foto.');
                            return;
                        }
                        this.selectedPhotos.push(filename);
                    }
                },

                openSubmitModal() {
                    if (this.selectedPhotos.length === 0) return;
                    this.submitModalOpen = true;
                },

                confirmSubmit() {
                    fetch("{{ route('booking.public.submit', ['bookingCode' => $bookingCode]) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            photos: this.selectedPhotos, // Mengirim array nama file murni
                            notes: this.clientNotes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            alert('Gagal mengirim pilihan: ' + (data.message || 'Terjadi kesalahan.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan pada server.');
                    });
                }
            }
        }
    </script>
</body>

</html>