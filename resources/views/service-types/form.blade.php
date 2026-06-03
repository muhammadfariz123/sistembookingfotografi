<x-app-layout>
    <div class="w-full h-[calc(100vh-65px)] flex flex-col justify-center px-4 sm:px-6 lg:px-8 bg-[#f5f7fb] overflow-hidden">
        
        <div class="w-full max-w-xl mx-auto"> <a href="{{ route('service-types.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 text-[13px] font-semibold transition mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Layanan
            </a>

            <div class="bg-white border border-gray-200 rounded-[20px] shadow-sm p-6">
                
                <h2 class="text-[20px] font-bold text-gray-900 mb-5 border-b border-gray-100 pb-4">
                    {{ isset($service) ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
                </h2>

                <form action="{{ isset($service) ? route('service-types.update', $service->id) : route('service-types.store') }}" method="POST">
                    @csrf
                    @if(isset($service))
                        @method('PUT')
                    @endif

                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                Nama Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required
                                value="{{ old('name', $service->name ?? '') }}"
                                placeholder="Contoh: Wedding, Prewedding, Wisuda"
                                class="w-full h-[44px] rounded-xl border border-gray-300 text-[14px] px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-sm">
                            @error('name')
                                <p class="text-red-500 text-[12px] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                Deskripsi <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea name="description" rows="2"
                                placeholder="Jelaskan detail paket layanan Anda..."
                                class="w-full rounded-xl border border-gray-300 text-[14px] px-4 py-2.5 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-sm">{{ old('description', $service->description ?? '') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-[12px] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ 
                                price: '{{ old('price', isset($service) ? $service->price : '') }}',
                                formatPrice(e) {
                                    let raw = e.target.value.replace(/\D/g, '');
                                    this.price = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                }
                            }" 
                            x-init="if(price) price = new Intl.NumberFormat('id-ID').format(price)">
                            
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                Harga Default <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            
                            <input type="hidden" name="price" :value="price.replace(/\D/g, '')">
                            
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-[14px] font-medium">Rp</span>
                                </div>
                                <input type="text" x-model="price" @input="formatPrice" placeholder="0"
                                    class="w-full h-[44px] rounded-xl border border-gray-300 pl-11 pr-4 text-[14px] font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-sm">
                            </div>
                            <p class="text-[12px] text-gray-400 mt-1.5">
                                Harga ini akan diisi otomatis saat membuat form booking baru.
                            </p>
                            @error('price')
                                <p class="text-red-500 text-[12px] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-6 pt-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('service-types.index') }}"
                            class="h-[44px] px-6 rounded-xl border border-gray-300 text-gray-600 font-semibold text-[14px] hover:bg-gray-50 flex items-center justify-center transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="h-[44px] px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                            {{ isset($service) ? 'Simpan Perubahan' : 'Simpan Layanan' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>