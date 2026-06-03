<x-app-layout>
    <div x-data="{ services: @js($services) }" 
         class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6 bg-[#f5f7fb] w-full flex flex-col overflow-hidden"
         style="height: calc(100vh - 65px);"> <div class="bg-white border border-gray-200 rounded-[20px] sm:rounded-[30px] shadow-sm p-4 sm:p-6 lg:p-8 flex flex-col flex-1 min-h-0 overflow-hidden">
            
            <div class="shrink-0 mb-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 text-[13px] sm:text-[14px] font-medium transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Dashboard
                </a>

                <div class="mt-4 sm:mt-5 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 sm:gap-6">
                    <div>
                        <h1 class="text-[22px] sm:text-[26px] lg:text-[30px] font-bold text-[#0f172a] leading-tight">
                            Kelola Jenis Layanan
                        </h1>
                        <p class="text-[13px] sm:text-[15px] text-gray-500 mt-1.5">
                            Tambahkan dan kelola jenis layanan yang Anda tawarkan
                        </p>
                    </div>
                    <a href="{{ route('service-types.create') }}"
                        class="h-[48px] px-5 sm:px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] sm:text-[15px] flex items-center justify-center gap-2 shadow-sm transition whitespace-nowrap w-full lg:w-auto shrink-0">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Layanan
                    </a>
                </div>
            </div>

            {{-- ── TABEL SECTION ── --}}
            <div class="border border-gray-200 rounded-[22px] flex-1 min-h-0 flex flex-col overflow-hidden relative">
                
                <div class="bg-[#f8fafc] border-b border-gray-200 shrink-0 pr-[scrollbar-width]"> 
                    <table class="w-full min-w-[500px]">
                        <thead>
                            <tr class="text-left">
                                <th class="w-1/3 px-4 lg:px-6 py-4 text-[12px] font-semibold tracking-wide text-gray-500 uppercase">Nama Layanan</th>
                                <th class="w-1/3 px-4 lg:px-6 py-4 text-[12px] font-semibold tracking-wide text-gray-500 uppercase">Deskripsi</th>
                                <th class="w-1/6 px-4 lg:px-6 py-4 text-[12px] font-semibold tracking-wide text-gray-500 uppercase">Harga Default</th>
                                <th class="w-1/6 px-4 lg:px-6 py-4 text-[12px] font-semibold tracking-wide text-gray-500 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="overflow-y-auto overflow-x-auto flex-1 no-scrollbar">
                    <table class="w-full min-w-[500px]">
                        <tbody class="divide-y divide-gray-200">
                            
                            <template x-if="services.length > 0">
                                <template x-for="item in services" :key="item.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="w-1/3 px-4 lg:px-6 py-4 sm:py-5">
                                            <p class="font-semibold text-[14px] lg:text-[15px] text-[#0f172a]" x-text="item.name"></p>
                                        </td>
                                        <td class="w-1/3 px-4 lg:px-6 py-4 sm:py-5">
                                            <p class="text-[13px] lg:text-[14px] text-gray-500 line-clamp-2" x-text="item.description || '-'"></p>
                                        </td>
                                        <td class="w-1/6 px-4 lg:px-6 py-4 sm:py-5 whitespace-nowrap">
                                            <p class="text-[13px] lg:text-[14px] font-medium text-[#0f172a]"
                                                x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.price || 0)"></p>
                                        </td>
                                        <td class="w-1/6 px-4 lg:px-6 py-4 sm:py-5">
                                            <div class="flex justify-end gap-3">
                                                <a :href="`/service-types/${item.id}/edit`"
                                                    class="w-9 h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition shrink-0">
                                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                                </a>

                                                <button type="button" @click="deleteServiceType(item.id)"
                                                    class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition shrink-0">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                                
                                                <form :id="`delete-form-${item.id}`" :action="`/service-types/${item.id}`" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                            
                            <template x-if="services.length === 0">
                                <tr>
                                    <td colspan="4">
                                        <div class="h-[320px] flex flex-col items-center justify-center px-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <h1 class="text-[20px] sm:text-[24px] font-bold text-gray-400">Belum ada layanan</h1>
                                            <p class="text-gray-400 mt-2 text-[13px] sm:text-[14px]">Klik Tambah Layanan untuk memulai</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons()
        })

        function deleteServiceType(id) {
            Swal.fire({
                title: 'Hapus Layanan?',
                text: 'Layanan yang dihapus tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-[28px]' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            })
        }
    </script>
    
    <style>
        /* Menghilangkan scrollbar tapi tetap bisa di scroll lewat trackpad/mouse wheel */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Cegah scrolling di body global saat berada di halaman ini */
        body {
            overflow: hidden !important;
        }
    </style>
</x-app-layout>