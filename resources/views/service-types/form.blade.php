<x-app-layout>
    <div class="w-full min-h-screen px-4 sm:px-6 lg:px-8 py-8 sm:py-10 bg-[#f5f7fb] overflow-hidden">
        <div class="w-full max-w-2xl mx-auto"> 
            
            <a href="{{ route('service-types.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 text-[13px] font-semibold transition mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Paket
            </a>

            <div class="bg-white border border-gray-200 rounded-[20px] shadow-sm p-6 relative">
                <h2 class="text-[20px] font-bold text-gray-900 mb-5 border-b border-gray-100 pb-4">
                    {{ isset($service) ? 'Edit Paket Layanan' : 'Tambah Paket Layanan' }}
                </h2>

                <form action="{{ isset($service) ? route('service-types.update', $service->id) : route('service-types.store') }}" method="POST">
                    @csrf
                    @if(isset($service)) @method('PUT') @endif

                    <div class="space-y-5">
                        
                        {{-- Pilih Kategori (Sekarang wajib memilih dari Kategori yang sudah dibuat di menu Portofolio) --}}
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Kategori Acara <span class="text-red-500">*</span></label>
                            <select name="service_category_id" required class="w-full h-[44px] rounded-xl border border-gray-300 text-[14px] px-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                                <option value="" disabled {{ !isset($service) ? 'selected' : '' }}>-- Pilih Kategori Acara --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (isset($service) && $service->service_category_id == $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1.5">Kategori tidak ada? Buat kategori baru di menu <b>Portofolio & Kategori</b>.</p>
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Nama Paket <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name', $service->name ?? '') }}" placeholder="Contoh: Paket Prewedding Basic" class="w-full h-[44px] rounded-xl border border-gray-300 text-[14px] px-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Fasilitas / Deskripsi <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="5" required placeholder="- Sesi Foto 4 Jam&#10;- Unlimited Shoot..." class="w-full rounded-xl border border-gray-300 text-[14px] px-4 py-3 resize-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('description', $service->description ?? '') }}</textarea>
                        </div>

                        <div x-data="{ 
                                price: '{{ old('price', isset($service) ? $service->price : '') }}',
                                formatPrice(e) {
                                    let raw = e.target.value.replace(/\D/g, '');
                                    this.price = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                }
                            }" 
                            x-init="if(price) price = new Intl.NumberFormat('id-ID').format(price)">
                            
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Harga Paket <span class="text-red-500">*</span></label>
                            <input type="hidden" name="price" :value="price.replace(/\D/g, '')">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-[14px] font-medium">Rp</span>
                                </div>
                                <input type="text" required x-model="price" @input="formatPrice" placeholder="0" class="w-full h-[44px] rounded-xl border border-gray-300 pl-11 pr-4 text-[14px] font-medium text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end gap-3">
                        <button type="submit" class="h-[44px] px-8 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[14px] rounded-xl shadow-sm transition-colors">
                            {{ isset($service) ? 'Simpan Perubahan' : 'Simpan Paket' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>