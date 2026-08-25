<x-app-layout>
    <div class="w-full min-h-screen px-4 sm:px-6 lg:px-8 py-8 sm:py-10 bg-[#f5f7fb] overflow-hidden">
        <div class="w-full max-w-2xl mx-auto">  
            
            <a href="{{ route('service-categories.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 text-[13px] font-semibold transition mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Kategori
            </a>

            <div class="bg-white border border-gray-200 rounded-[20px] shadow-sm p-6 relative">
                <h2 class="text-[20px] font-bold text-gray-900 mb-5 border-b border-gray-100 pb-4">
                    {{ isset($category) ? 'Edit Kategori & Portofolio' : 'Tambah Kategori Baru' }}
                </h2>

                <form action="{{ isset($category) ? route('service-categories.update', $category->id) : route('service-categories.store') }}" method="POST" enctype="multipart/form-data" x-data="galleryManager()" @submit="isSubmitting = true">
                    @csrf
                    @if(isset($category)) @method('PUT') @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Nama Kategori / Jenis Acara <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name', $category->name ?? '') }}" placeholder="Contoh: Wedding, Wisuda, Prewedding..." class="w-full h-[44px] rounded-xl border border-gray-300 text-[14px] px-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>

                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Upload Portofolio Foto Baru</label>
                            
                            <div class="flex items-center justify-center w-full mb-4">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-blue-400 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <p class="text-sm text-gray-500"><span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag & drop</p>
                                        <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 10MB (Bisa pilih banyak sekaligus)</p>
                                    </div>
                                    <input type="file" name="galleries[]" multiple accept="image/*" class="hidden" x-ref="fileInput" @change="addFiles" />
                                </label>
                            </div>

                            <!-- Preview Foto Baru -->
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4 mb-4" x-show="previewUrls.length > 0">
                                <template x-for="(file, index) in previewUrls" :key="index">
                                    <div class="relative aspect-square rounded-xl bg-gray-100 border border-gray-200 overflow-hidden group">
                                        <img :src="file.url" class="w-full h-full object-cover">
                                        <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition shadow-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                        <div class="absolute bottom-0 inset-x-0 bg-black/50 text-white text-[10px] truncate px-2 py-1" x-text="file.name"></div>
                                    </div>
                                </template>
                            </div>

                            @if(isset($category) && $category->galleries->count() > 0)
                                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <label class="block text-[13px] font-medium text-gray-700">Foto Tersimpan di Cloudinary</label>
                                    <button type="button" @click="toggleSelectAll" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition" x-text="selectAll ? 'Batal Pilih Semua' : 'Pilih Semua untuk Hapus'"></button>
                                </div>
                                
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4 mt-2">
                                    @foreach($category->galleries as $gallery)
                                        <div class="relative aspect-square rounded-xl bg-gray-100 border border-gray-200 overflow-hidden group" id="gallery-{{ $gallery->id }}">
                                            <!-- DIPERBAIKI: Langsung panggil $gallery->image_path karena isinya sudah URL Cloudinary utuh -->
                                            <img src="{{ $gallery->image_path }}" class="w-full h-full object-cover">
                                            
                                            <!-- Checkbox untuk Pilih Massal -->
                                            <div class="absolute top-2 left-2 z-10">
                                                <input type="checkbox" name="selected_galleries[]" value="{{ $gallery->id }}" x-model="selectedPhotos" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 shadow-sm cursor-pointer">
                                            </div>

                                            <button type="button" onclick="deleteExistingPhoto({{ $gallery->id }})" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition shadow-md" title="Hapus foto ini">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Tombol Hapus Terpilih Massal -->
                                <div class="mt-3" x-show="selectedPhotos.length > 0" x-cloak>
                                    <button type="button" @click="deleteSelectedPhotos" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus Foto Terpilih (<span x-text="selectedPhotos.length"></span>)
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('service-categories.index') }}" class="h-[44px] px-6 rounded-xl border border-gray-300 text-gray-600 font-semibold text-[14px] hover:bg-gray-50 flex items-center justify-center transition-colors">Batal</a>
                        <button type="submit" :disabled="isSubmitting" class="h-[44px] px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2 disabled:opacity-50">
                            <span x-show="!isSubmitting">{{ isset($category) ? 'Simpan Perubahan' : 'Simpan Kategori' }}</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2" x-cloak>
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Menyimpan ke Cloud...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function compressImage(file, maxWidth = 1600, maxHeight = 1600, quality = 0.8) {
            return new Promise((resolve) => {
                if (!file.type.startsWith('image/')) {
                    resolve(file);
                    return;
                }

                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > maxWidth) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width = Math.round((width * maxHeight) / height);
                                height = maxHeight;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                resolve(file);
                                return;
                            }
                            // Ganti ekstensi file menjadi .jpg karena dikompres ke jpeg
                            const newName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                            const compressedFile = new File([blob], newName, {
                                type: 'image/jpeg',
                                lastModified: Date.now(),
                            });
                            resolve(compressedFile);
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => resolve(file);
                };
                reader.onerror = () => resolve(file);
            });
        }

        function galleryManager() {
            return {
                previewUrls: [], files: [], selectedPhotos: [], selectAll: false, isSubmitting: false,
                async addFiles(e) {
                    const selectedFiles = Array.from(e.target.files);
                    for (let file of selectedFiles) {
                        const compressedFile = await compressImage(file);
                        this.files.push(compressedFile);
                        this.previewUrls.push({ name: compressedFile.name, url: URL.createObjectURL(compressedFile) });
                    }
                    this.syncInputFiles();
                },
                removeFile(index) {
                    this.files.splice(index, 1);
                    this.previewUrls.splice(index, 1);
                    this.syncInputFiles();
                },
                syncInputFiles() {
                    const dt = new DataTransfer();
                    this.files.forEach(file => dt.items.add(file));
                    this.$refs.fileInput.files = dt.files;
                },
                toggleSelectAll() {
                    this.selectAll = !this.selectAll;
                    const checkboxes = document.querySelectorAll('input[name="selected_galleries[]"]');
                    if (this.selectAll) {
                        this.selectedPhotos = Array.from(checkboxes).map(cb => cb.value);
                    } else {
                        this.selectedPhotos = [];
                    }
                },
                deleteSelectedPhotos() {
                    if(this.selectedPhotos.length === 0) return;
                    if(!confirm(`Yakin ingin menghapus ${this.selectedPhotos.length} foto terpilih?`)) return;

                    fetch('/category-galleries/bulk-delete', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                            'Accept': 'application/json' 
                        },
                        body: JSON.stringify({ ids: this.selectedPhotos })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.selectedPhotos.forEach(id => {
                                document.getElementById(`gallery-${id}`).remove();
                            });
                            this.selectedPhotos = [];
                            alert('Foto terpilih berhasil dihapus!');
                        } else {
                            alert('Gagal menghapus beberapa foto.');
                        }
                    })
                    .catch(err => alert('Terjadi kesalahan jaringan'));
                }
            }
        }

        function deleteExistingPhoto(galleryId) {
            if(!confirm('Yakin ingin menghapus foto ini?')) return;
            fetch(`/category-galleries/${galleryId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => { document.getElementById(`gallery-${galleryId}`).remove(); })
            .catch(err => alert('Gagal menghapus foto'));
        }
    </script>
</x-app-layout>