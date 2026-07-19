<x-app-layout>
    <div class="w-full min-h-screen px-4 sm:px-6 lg:px-8 py-8 sm:py-10 bg-[#f5f7fb] overflow-hidden">
        <div class="w-full max-w-2xl mx-auto"> 
            
            <a href="{{ route('service-types.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 text-[13px] font-semibold transition mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Layanan
            </a>

            <div class="bg-white border border-gray-200 rounded-[20px] shadow-sm p-6 relative">
                
                <h2 class="text-[20px] font-bold text-gray-900 mb-5 border-b border-gray-100 pb-4">
                    {{ isset($service) ? 'Edit Layanan & Galeri' : 'Tambah Layanan Baru' }}
                </h2>

                {{-- PENTING: Tambahkan enctype multipart/form-data untuk upload --}}
                <form action="{{ isset($service) ? route('service-types.update', $service->id) : route('service-types.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($service)) @method('PUT') @endif

                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Nama Layanan <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name', $service->name ?? '') }}" placeholder="Contoh: Wedding 1 Hari" class="w-full h-[44px] rounded-xl border border-gray-300 text-[14px] px-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Deskripsi <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="5" required placeholder="- Sesi Foto 8 Jam&#10;- Unlimited Shoot..." class="w-full rounded-xl border border-gray-300 text-[14px] px-4 py-3 resize-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('description', $service->description ?? '') }}</textarea>
                        </div>

                        <div x-data="{ 
                                price: '{{ old('price', isset($service) ? $service->price : '') }}',
                                formatPrice(e) {
                                    let raw = e.target.value.replace(/\D/g, '');
                                    this.price = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                }
                            }" 
                            x-init="if(price) price = new Intl.NumberFormat('id-ID').format(price)">
                            
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Harga Default <span class="text-red-500">*</span></label>
                            <input type="hidden" name="price" :value="price.replace(/\D/g, '')">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-[14px] font-medium">Rp</span>
                                </div>
                                <input type="text" required x-model="price" @input="formatPrice" placeholder="0" class="w-full h-[44px] rounded-xl border border-gray-300 pl-11 pr-4 text-[14px] font-medium text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                        </div>

                        {{-- SEKSI UPLOAD GALERI FOTO --}}
                        <div class="pt-4 mt-4 border-t border-gray-100" x-data="galleryManager()">
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Upload Hasil Foto (Galeri)</label>
                            
                            <div class="flex items-center justify-center w-full mb-4">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-blue-400 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <p class="text-sm text-gray-500"><span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag & drop</p>
                                        <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 3MB (Bisa pilih banyak sekaligus)</p>
                                    </div>
                                    <input type="file" name="galleries[]" multiple accept="image/*" class="hidden" x-ref="fileInput" @change="addFiles" />
                                </label>
                            </div>

                            {{-- Preview Foto Baru yang akan diupload (Disilang) --}}
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4 mb-4">
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

                            {{-- Tampilkan Foto Lama Jika Sedang Edit --}}
                            @if(isset($service) && $service->galleries->count() > 0)
                                <label class="block text-[13px] font-medium text-gray-700 mb-2 mt-6">Foto Tersimpan (Klik silang untuk hapus permanen)</label>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
                                    @foreach($service->galleries as $gallery)
                                        <div class="relative aspect-square rounded-xl bg-gray-100 border border-gray-200 overflow-hidden group" id="gallery-{{ $gallery->id }}">
                                            <img src="{{ Storage::url($gallery->image_path) }}" class="w-full h-full object-cover">
                                            <button type="button" onclick="deleteExistingPhoto({{ $gallery->id }})" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition shadow-md">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>

                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('service-types.index') }}" class="h-[44px] px-6 rounded-xl border border-gray-300 text-gray-600 font-semibold text-[14px] hover:bg-gray-50 flex items-center justify-center transition-colors">Batal</a>
                        <button type="submit" class="h-[44px] px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                            {{ isset($service) ? 'Simpan Perubahan' : 'Simpan Layanan' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        // AlpineJS logic untuk manipulasi file input (Preview & Silang)
        function galleryManager() {
            return {
                previewUrls: [],
                files: [],
                addFiles(e) {
                    const selectedFiles = Array.from(e.target.files);
                    selectedFiles.forEach(file => {
                        this.files.push(file);
                        this.previewUrls.push({
                            name: file.name,
                            url: URL.createObjectURL(file)
                        });
                    });
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
                }
            }
        }

        // JS Native untuk menghapus foto lama via AJAX
        function deleteExistingPhoto(galleryId) {
            if(!confirm('Yakin ingin menghapus foto ini?')) return;
            
            fetch(`/service-galleries/${galleryId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById(`gallery-${galleryId}`).remove();
            })
            .catch(err => alert('Gagal menghapus foto'));
        }
    </script>
</x-app-layout>