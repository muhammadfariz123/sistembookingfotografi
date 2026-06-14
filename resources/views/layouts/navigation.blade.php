{{-- resources/views/layouts/navigation.blade.php --}}
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 w-full sticky top-0 z-50 shadow-sm">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-3.5">
            {{-- KIRI: Brand & Info --}}
            <div class="flex items-center gap-3 sm:gap-5 min-w-0">
                <a href="{{ route('dashboard') }}" class="text-[18px] sm:text-[20px] font-extrabold text-blue-600 whitespace-nowrap tracking-tight">
                    Rozi Photography
                </a>
                <div class="hidden sm:block h-6 w-px bg-gray-200"></div>
                <span class="hidden sm:block text-[13px] text-gray-600 truncate">
                    Halo, <b class="font-bold text-gray-900">{{ Auth::user()->email }}</b>
                </span>
            </div>

            {{-- KANAN: Ikon Desktop (Diberi Teks agar Jelas) --}}
            <div class="hidden lg:flex items-center gap-2 text-gray-600">
                <a href="{{ route('financial.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition font-medium text-[13px]">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Laporan Keuangan
                </a>
                <a href="{{ route('bookings.export') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-emerald-50 hover:text-emerald-600 transition font-medium text-[13px]">
                    <i data-lucide="download" class="w-4 h-4"></i> Unduh Excel
                </a>
                <a href="{{ route('company-setting.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-100 transition font-medium text-[13px]">
                    <i data-lucide="settings" class="w-4 h-4"></i> Pengaturan
                </a>
                
                <div class="h-5 w-px bg-gray-200 mx-2"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-3 py-2 rounded-xl text-red-500 hover:bg-red-50 transition font-medium text-[13px]">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                    </button>
                </form>
            </div>

            {{-- TOMBOL MOBILE MENU --}}
            <div class="flex lg:hidden items-center gap-3">
                <button @click="open = !open" class="p-2 rounded-xl text-gray-600 hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <i x-show="!open" data-lucide="menu" class="w-5 h-5"></i>
                    <i x-show="open" data-lucide="x" class="w-5 h-5" x-cloak></i>
                </button>
            </div>
        </div>

        {{-- SAPAAN MOBILE --}}
        <div class="sm:hidden pb-3 -mt-1">
            <span class="text-[13px] text-gray-600 block truncate">
                Halo, <b class="font-bold text-gray-900">{{ Auth::user()->email }}</b>
            </span>
        </div>

        {{-- MENU MOBILE (Tampil ke Bawah) --}}
        <div x-show="open" x-collapse class="lg:hidden border-t border-gray-100 pt-3 pb-4">
            <div class="flex flex-col gap-1">
                <a href="{{ route('financial.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i> Laporan Keuangan
                </a>
                <a href="{{ route('bookings.export') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 transition">
                    <i data-lucide="download" class="w-5 h-5"></i> Unduh Excel Data
                </a>
                <a href="{{ route('company-setting.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                    <i data-lucide="settings" class="w-5 h-5"></i> Pengaturan Perusahaan
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-gray-100 pt-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition">
                        <i data-lucide="log-out" class="w-5 h-5"></i> Keluar (Logout)
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () { lucide.createIcons(); });
    document.addEventListener('alpine:updated', function () { lucide.createIcons(); });
</script>