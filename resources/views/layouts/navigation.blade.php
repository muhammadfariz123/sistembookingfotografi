{{-- LUCIDE ICONS via CDN --}}
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-3">
            {{-- KIRI --}}
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <a href="{{ route('dashboard') }}"
                    class="text-[17px] sm:text-[18px] font-bold text-blue-600 whitespace-nowrap">
                    Rozi Photography
                </a>
                <span class="hidden sm:block text-[13px] text-gray-700 truncate">
                    Halo,
                    <b class="font-semibold text-gray-900">{{ Auth::user()->email }}</b>
                </span>
            </div>

            {{-- KANAN: IKON DESKTOP --}}
            <div class="hidden md:flex items-center gap-6 text-gray-500">
                {{-- Laporan Keuangan --}}
                <div class="relative group">
                    <a href="{{ route('financial.index') }}"
                        class="cursor-pointer hover:text-blue-600 transition block">
                        <i data-lucide="bar-chart-3" style="width:18px;height:18px;"></i>
                    </a>
                    <span
                        class="absolute top-full mt-2 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[11px] px-2 py-1 rounded shadow-sm opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-20">
                        Laporan Keuangan
                    </span>
                </div>

                {{-- Download Excel — export semua data booking --}}
                <div class="relative group">
                    <a href="{{ route('financial.index', ['export' => 1]) }}"
                        class="cursor-pointer hover:text-blue-600 transition block">
                        <i data-lucide="download" style="width:18px;height:18px;"></i>
                    </a>
                    <span
                        class="absolute top-full mt-2 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[11px] px-2 py-1 rounded shadow-sm opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-20">
                        Unduh Excel
                    </span>
                </div>

                {{-- Pengaturan Perusahaan --}}
                <div class="relative group">
                    <a href="{{ route('company-setting.edit') }}"
                        class="cursor-pointer hover:text-blue-600 transition block">
                        <i data-lucide="settings" style="width:18px;height:18px;"></i>
                    </a>
                    <span
                        class="absolute top-full mt-2 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[11px] px-2 py-1 rounded shadow-sm opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-20">
                        Pengaturan Perusahaan
                    </span>
                </div>

                {{-- Logout --}}
                <div class="relative group">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="cursor-pointer hover:text-red-500 transition block">
                            <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                        </button>
                    </form>
                    <span
                        class="absolute top-full mt-2 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[11px] px-2 py-1 rounded shadow-sm opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-20">
                        Logout
                    </span>
                </div>
            </div>

            {{-- MOBILE MENU BUTTON --}}
            <div class="flex md:hidden items-center gap-3">
                <button @click="open = !open" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                    <i x-show="!open" data-lucide="menu" style="width:20px;height:20px;"></i>
                    <i x-show="open" data-lucide="x" style="width:20px;height:20px;"></i>
                </button>
            </div>
        </div>

        {{-- SAPAAN MOBILE --}}
        <div class="sm:hidden pb-3 -mt-1">
            <span class="text-[13px] text-gray-700 block truncate">
                Halo,
                <b class="font-semibold text-gray-900">{{ Auth::user()->email }}</b>
            </span>
        </div>

        {{-- MENU MOBILE --}}
        <div x-show="open" class="md:hidden border-t border-gray-100 pt-4 pb-4">
            <div class="grid grid-cols-1 gap-2">
                <a href="{{ route('financial.index') }}"
                    class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i data-lucide="bar-chart-3" style="width:18px;height:18px;"></i>
                    <span>Laporan Keuangan</span>
                </a>

                {{-- Download Excel mobile --}}
                <a href="{{ route('financial.index', ['export' => 1]) }}"
                    class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i data-lucide="download" style="width:18px;height:18px;"></i>
                    <span>Unduh Excel</span>
                </a>

                {{-- Pengaturan Perusahaan Mobile --}}
                <a href="{{ route('company-setting.edit') }}"
                    class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i data-lucide="settings" style="width:18px;height:18px;"></i>
                    <span>Pengaturan Perusahaan</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-sm text-red-500 hover:bg-red-50 transition">
                        <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
    document.addEventListener('alpine:updated', function () {
        lucide.createIcons();
    });
</script>