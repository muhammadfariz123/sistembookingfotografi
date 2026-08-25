<x-app-layout>
    <div x-data="{ categories: @js($categories) }" 
         class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6 bg-[#f5f7fb] w-full flex flex-col overflow-hidden index-container"> 
         
         <div class="bg-white border border-gray-200 rounded-[20px] sm:rounded-[30px] shadow-sm p-4 sm:p-6 lg:p-8 flex flex-col flex-1 min-h-0 overflow-hidden">
            
            <div class="shrink-0 mb-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 text-[13px] sm:text-[14px] font-medium transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Dashboard
                </a>

                <div class="mt-4 sm:mt-5 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 sm:gap-6">
                    <div>
                        <h1 class="text-[22px] sm:text-[26px] lg:text-[30px] font-bold text-[#0f172a] leading-tight">
                            Kategori & Portofolio
                        </h1>
                        <p class="text-[13px] sm:text-[15px] text-gray-500 mt-1.5">
                            Kelola jenis acara dan unggah contoh hasil foto (Portofolio) Anda di sini.
                        </p>
                    </div>
                    <a href="{{ route('service-categories.create') }}"
                        class="h-[48px] px-5 sm:px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] sm:text-[15px] flex items-center justify-center gap-2 shadow-sm transition whitespace-nowrap w-full lg:w-auto shrink-0">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
                    </a>
                </div>
            </div>

            <div class="border border-gray-200 rounded-[22px] flex-1 min-h-0 flex flex-col overflow-hidden relative">
                <div class="bg-[#f8fafc] border-b border-gray-200 shrink-0 pr-[scrollbar-width]"> 
                    <table class="w-full min-w-[500px]">
                        <thead>
                            <tr class="text-left">
                                <th class="w-1/2 px-4 lg:px-6 py-4 text-[12px] font-semibold tracking-wide text-gray-500 uppercase">Jenis Kategori</th>
                                <th class="w-1/4 px-4 lg:px-6 py-4 text-[12px] font-semibold tracking-wide text-gray-500 uppercase">Jumlah Foto</th>
                                <th class="w-1/4 px-4 lg:px-6 py-4 text-[12px] font-semibold tracking-wide text-gray-500 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="overflow-y-auto overflow-x-auto flex-1 no-scrollbar">
                    <table class="w-full min-w-[500px]">
                        <tbody class="divide-y divide-gray-200" x-init="$watch('categories', () => $nextTick(() => lucide.createIcons()))">
                            
                            <template x-if="categories.length > 0">
                                <template x-for="item in categories" :key="item.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="w-1/2 px-4 lg:px-6 py-4 sm:py-5 align-top">
                                            <p class="font-bold text-[15px] text-[#0f172a]" x-text="item.name"></p>
                                        </td>
                                        <td class="w-1/4 px-4 lg:px-6 py-4 sm:py-5 align-top">
                                            <div class="flex items-center gap-1.5 text-[12px] font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-lg w-fit whitespace-nowrap border border-blue-100">
                                                <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                                <span x-text="item.galleries ? item.galleries.length : 0"></span> Foto
                                            </div>
                                        </td>
                                        <td class="w-1/4 px-4 lg:px-6 py-4 sm:py-5 align-top">
                                            <div class="flex justify-end gap-3">
                                                <a :href="`/service-categories/${item.id}/edit`"
                                                    class="w-9 h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition shrink-0 border border-blue-100">
                                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                                </a>
                                                <button type="button" @click="deleteCategory(item.id)"
                                                    class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition shrink-0 border border-red-100">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                                <form :id="`delete-form-${item.id}`" :action="`/service-categories/${item.id}`" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                            
                            <template x-if="categories.length === 0">
                                <tr>
                                    <td colspan="3">
                                        <div class="h-[320px] flex flex-col items-center justify-center px-4 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z" />
                                            </svg>
                                            <h1 class="text-[20px] sm:text-[24px] font-bold text-gray-400">Belum ada Kategori</h1>
                                            <p class="text-gray-400 mt-2 text-[13px] sm:text-[14px]">Klik Tambah Kategori untuk memulai</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
            setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 100);
        });

        function deleteCategory(id) {
            Swal.fire({
                title: 'Hapus Kategori?',
                text: 'Kategori beserta semua foto portofolionya akan terhapus permanen.',
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
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        @media (min-width: 1024px) {
            .index-container {
                height: calc(100vh - 65px);
            }
            body {
                overflow: hidden !important;
            }
        }
    </style>
</x-app-layout>