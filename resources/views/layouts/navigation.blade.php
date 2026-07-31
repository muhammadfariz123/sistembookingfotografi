{{-- resources/views/layouts/navigation.blade.php --}}
{{-- OVERLAY GELAP UNTUK MOBILE --}}
<div x-show="sidebarMobileOpen" 
     x-transition.opacity 
     @click="sidebarMobileOpen = false"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-cloak>
</div>

{{-- SIDEBAR KIRI --}}
{{-- Lebar berubah dinamis: 260px jika terbuka, 80px jika collapsed (HANYA di desktop) --}}
<aside x-data="{ isDesktop: window.innerWidth >= 1024 }"
       x-init="
            window.addEventListener('resize', () => {
                isDesktop = window.innerWidth >= 1024;
            });
       "
       :class="[
        sidebarMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-[80px]' : 'lg:w-[260px]'
       ]"
       x-effect="if (sidebarMobileOpen) { sidebarCollapsed = false }"
       class="sidebar-transition fixed lg:static inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-gray-100 flex flex-col h-screen shadow-xl lg:shadow-none overflow-visible">
    
    {{-- HEADER: TOMBOL TOGGLE + NAMA APLIKASI (Horizontal, tanpa logo kamera) --}}
    <div class="h-[72px] flex items-center justify-between px-4 border-b border-gray-100 shrink-0">
        
        <div class="flex items-center gap-2 min-w-0" :class="sidebarCollapsed ? 'lg:justify-center lg:w-full' : ''">
            {{-- Tombol Toggle Desktop (Panah kiri-kanan) --}}
            <button @click="sidebarCollapsed = !sidebarCollapsed" 
                    title="Buka/Tutup Sidebar"
                    class="hidden lg:flex w-8 h-8 shrink-0 items-center justify-center rounded-lg text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition">
                <i data-lucide="chevron-left" class="w-4 h-4 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
            </button>
            {{-- Nama Aplikasi --}}
            <a href="{{ route('dashboard') }}" 
               x-show="!(sidebarCollapsed && isDesktop)"
               class="text-[18px] font-extrabold text-blue-600 whitespace-nowrap truncate">
                Rozi Photo
            </a>
        </div>
        {{-- Tombol Tutup Mobile --}}
        <button @click="sidebarMobileOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600 shrink-0">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    {{-- AREA MENU (Bisa di-scroll jika panjang) --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden py-6 px-3 flex flex-col gap-6 no-scrollbar" :class="(sidebarCollapsed && isDesktop) ? 'items-center' : ''">
        
        {{-- MENU 1: DASHBOARD (Berdiri Sendiri) --}}
        <div class="w-full">
            <div class="flex flex-col gap-1 w-full" :class="(sidebarCollapsed && isDesktop) ? 'items-center' : ''">
                
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" 
                   title="Dashboard"
                   class="flex items-center px-3 py-2.5 rounded-xl font-medium text-[13px] transition w-full group relative
                   {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="(sidebarCollapsed && isDesktop) ? 'justify-center w-11 h-11 px-0' : 'gap-3'">
                    <i data-lucide="home" class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600' }}"></i> 
                    <span x-show="!(sidebarCollapsed && isDesktop)" class="whitespace-nowrap">Dashboard</span>
                </a>
            </div>
        </div>

        {{-- MENU 2 BARU: KATALOG LAYANAN (DROPDOWN) --}}
        @php
            $katalogActive = request()->routeIs('service-categories.*', 'service-types.*');
        @endphp
        <div class="w-full" 
             x-data="{ 
                open: {{ $katalogActive ? 'true' : 'false' }} || ['/service-categories', '/service-types'].some(p => window.location.pathname.startsWith(p)),
                isActive: {{ $katalogActive ? 'true' : 'false' }} || ['/service-categories', '/service-types'].some(p => window.location.pathname.startsWith(p)), 
                flyoutStyle: '' 
             }"
             x-init="
                $watch('sidebarCollapsed', (val) => {
                    if (isDesktop) {
                        if (val) { open = false }
                        else if (isActive) { open = true }
                    }
                });
                $watch('sidebarMobileOpen', (val) => {
                    if (val && isActive) { open = true }
                });
                $watch('isDesktop', (val) => {
                    if (!val) {
                        if (isActive) { open = true }
                    } else {
                        if (sidebarCollapsed) { open = false }
                        else if (isActive) { open = true }
                    }
                });
             "
             @click.outside="open = false">
            
            <div class="flex flex-col gap-1 w-full" :class="(sidebarCollapsed && isDesktop) ? 'items-center' : ''">
                
                {{-- Induk Katalog --}}
                <button @click="
                            if (sidebarCollapsed && isDesktop) {
                                open = !open;
                                $nextTick(() => {
                                    const rect = $el.getBoundingClientRect();
                                    flyoutStyle = `top: ${rect.top}px; left: ${rect.right + 10}px;`;
                                });
                            } else {
                                open = !open;
                            }
                        " 
                        title="Katalog Layanan"
                        class="flex items-center px-3 py-2.5 rounded-xl font-medium text-[13px] transition w-full group focus:outline-none
                        {{ $katalogActive ? 'bg-blue-50/50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                        :class="(sidebarCollapsed && isDesktop) ? 'justify-center w-11 h-11 px-0' : 'justify-between'">
                    
                    <div class="flex items-center" :class="(sidebarCollapsed && isDesktop) ? 'justify-center' : 'gap-3'">
                        <i data-lucide="layers" class="w-5 h-5 shrink-0 {{ $katalogActive ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        <span x-show="!(sidebarCollapsed && isDesktop)" class="whitespace-nowrap">Katalog Layanan</span>
                    </div>
                    <i x-show="!(sidebarCollapsed && isDesktop)" data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                {{-- Sub-Menu Inline --}}
                <div x-show="open && !(sidebarCollapsed && isDesktop)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="pl-9 pr-2 py-1 flex flex-col gap-1 mt-1">
                        
                        <a href="{{ route('service-categories.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition 
                           {{ request()->routeIs('service-categories.*') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('service-categories.*') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            Kategori & Portofolio
                        </a>
                        <a href="{{ route('service-types.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition 
                           {{ request()->routeIs('service-types.*') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('service-types.*') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            Daftar Paket
                        </a>
                    </div>
                </div>

                {{-- Flyout Popup Desktop --}}
                <div x-show="open && (sidebarCollapsed && isDesktop)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-x-1"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     :style="flyoutStyle"
                     class="fixed w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-[100]"
                     x-cloak>
                    <div class="px-4 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Katalog</div>
                    
                    <a href="{{ route('service-categories.index') }}" 
                       @click="open = false"
                       class="flex items-center gap-3 px-4 py-2.5 text-[13px] transition
                       {{ request()->routeIs('service-categories.*') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('service-categories.*') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                        Kategori & Portofolio
                    </a>
                    <a href="{{ route('service-types.index') }}" 
                       @click="open = false"
                       class="flex items-center gap-3 px-4 py-2.5 text-[13px] transition
                       {{ request()->routeIs('service-types.*') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('service-types.*') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                        Daftar Paket
                    </a>
                </div>
            </div>
        </div>

        {{-- MENU 3: OPERASIONAL (DROPDOWN INLINE / FLYOUT POPUP) --}}
        @php
            $operasionalActive = request()->routeIs('bookings.listPage', 'bookings.calendar', 'transactions.index', 'financial.index');
        @endphp
        <div class="w-full" 
             x-data="{ 
                // GANDA: dicek dari PHP (route name) DAN dari URL browser (path prefix),
                // supaya tetap benar walau nama route di Laravel meleset/berubah.
                open: {{ $operasionalActive ? 'true' : 'false' }} || ['/bookings', '/transactions', '/financial'].some(p => window.location.pathname.startsWith(p)),
                isActive: {{ $operasionalActive ? 'true' : 'false' }} || ['/bookings', '/transactions', '/financial'].some(p => window.location.pathname.startsWith(p)),
                flyoutStyle: '' 
             }"
             x-init="
                // Saat mode collapse desktop berubah
                $watch('sidebarCollapsed', (val) => {
                    if (isDesktop) {
                        if (val) { open = false }
                        else if (isActive) { open = true }
                    }
                });
                // Saat drawer mobile dibuka, pastikan menu aktif selalu ter-expand
                $watch('sidebarMobileOpen', (val) => {
                    if (val && isActive) { open = true }
                });
                // Saat breakpoint berpindah (mis. resize / rotate device)
                $watch('isDesktop', (val) => {
                    if (!val) {
                        // Masuk mode mobile/tablet: submenu ikut status aktif halaman, bukan status collapse desktop
                        if (isActive) { open = true }
                    } else {
                        if (sidebarCollapsed) { open = false }
                        else if (isActive) { open = true }
                    }
                });
             "
             @click.outside="open = false">
            
            <div class="flex flex-col gap-1 w-full" :class="(sidebarCollapsed && isDesktop) ? 'items-center' : ''">
                
                {{-- Induk Operasional --}}
                <button @click="
                            if (sidebarCollapsed && isDesktop) {
                                open = !open;
                                $nextTick(() => {
                                    const rect = $el.getBoundingClientRect();
                                    flyoutStyle = `top: ${rect.top}px; left: ${rect.right + 10}px;`;
                                });
                            } else {
                                open = !open;
                            }
                        " 
                        title="Operasional"
                        class="flex items-center px-3 py-2.5 rounded-xl font-medium text-[13px] transition w-full group focus:outline-none
                        {{ request()->routeIs('bookings.listPage', 'bookings.calendar', 'transactions.index', 'financial.index') ? 'bg-blue-50/50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                        :class="(sidebarCollapsed && isDesktop) ? 'justify-center w-11 h-11 px-0' : 'justify-between'">
                    
                    <div class="flex items-center" :class="(sidebarCollapsed && isDesktop) ? 'justify-center' : 'gap-3'">
                        <i data-lucide="briefcase" class="w-5 h-5 shrink-0 {{ request()->routeIs('bookings.listPage', 'bookings.calendar', 'transactions.index', 'financial.index') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        <span x-show="!(sidebarCollapsed && isDesktop)" class="whitespace-nowrap">Operasional</span>
                    </div>
                    <i x-show="!(sidebarCollapsed && isDesktop)" data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                {{-- Sub-Menu Inline (Sidebar Lebar ATAU Mobile Drawer) --}}
                <div x-show="open && !(sidebarCollapsed && isDesktop)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="pl-9 pr-2 py-1 flex flex-col gap-1 mt-1">
                        
                        <a href="{{ route('bookings.listPage') }}" 
                           class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-[13px] transition 
                           {{ request()->routeIs('bookings.listPage') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('bookings.listPage') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                                Booking
                            </div>
                            @if($sidebarPendingBookingCount > 0)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center bg-amber-100 text-amber-700">
                                    {{ $sidebarPendingBookingCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('bookings.calendar') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition 
                           {{ request()->routeIs('bookings.calendar') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('bookings.calendar') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            Kalender
                        </a>
                        <a href="{{ route('transactions.index') }}" 
                           class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-[13px] transition 
                           {{ request()->routeIs('transactions.index') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('transactions.index') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                                Transaksi Pembayaran
                            </div>
                            @if($sidebarPendingPaymentCount > 0)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center bg-amber-100 text-amber-700">
                                    {{ $sidebarPendingPaymentCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('financial.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition 
                           {{ request()->routeIs('financial.index') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('financial.index') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            Laporan Keuangan
                        </a>
                    </div>
                </div>

                {{-- Flyout Popup (Sidebar Collapsed, DESKTOP SAJA) --}}
                <div x-show="open && (sidebarCollapsed && isDesktop)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-x-1"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     :style="flyoutStyle"
                     class="fixed w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-[100]"
                     x-cloak>
                    <div class="px-4 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Operasional</div>
                    
                    <a href="{{ route('bookings.listPage') }}" 
                       @click="open = false"
                       class="flex items-center justify-between gap-3 px-4 py-2.5 text-[13px] transition
                       {{ request()->routeIs('bookings.listPage') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('bookings.listPage') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            Booking
                        </div>
                        @if($sidebarPendingBookingCount > 0)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center bg-amber-100 text-amber-700">
                                {{ $sidebarPendingBookingCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('bookings.calendar') }}" 
                       @click="open = false"
                       class="flex items-center gap-3 px-4 py-2.5 text-[13px] transition
                       {{ request()->routeIs('bookings.calendar') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('bookings.calendar') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                        Kalender
                    </a>
                    <a href="{{ route('transactions.index') }}" 
                       @click="open = false"
                       class="flex items-center justify-between gap-3 px-4 py-2.5 text-[13px] transition
                       {{ request()->routeIs('transactions.index') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('transactions.index') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            Transaksi Pembayaran
                        </div>
                        @if($sidebarPendingPaymentCount > 0)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center bg-amber-100 text-amber-700">
                                {{ $sidebarPendingPaymentCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('financial.index') }}" 
                       @click="open = false"
                       class="flex items-center gap-3 px-4 py-2.5 text-[13px] transition
                       {{ request()->routeIs('financial.index') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('financial.index') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                        Laporan Keuangan
                    </a>
                </div>
            </div>
        </div>

        {{-- MENU 4: SISTEM (DROPDOWN INLINE / FLYOUT POPUP) --}}
        @php
            $sistemActive = request()->routeIs('company-setting.edit');
        @endphp
        <div class="w-full" 
             x-data="{ 
                open: {{ $sistemActive ? 'true' : 'false' }} || ['/company-setting'].some(p => window.location.pathname.startsWith(p)),
                isActive: {{ $sistemActive ? 'true' : 'false' }} || ['/company-setting'].some(p => window.location.pathname.startsWith(p)), 
                flyoutStyle: '' 
             }"
             x-init="
                $watch('sidebarCollapsed', (val) => {
                    if (isDesktop) {
                        if (val) { open = false }
                        else if (isActive) { open = true }
                    }
                });
                $watch('sidebarMobileOpen', (val) => {
                    if (val && isActive) { open = true }
                });
                $watch('isDesktop', (val) => {
                    if (!val) {
                        if (isActive) { open = true }
                    } else {
                        if (sidebarCollapsed) { open = false }
                        else if (isActive) { open = true }
                    }
                });
             "
             @click.outside="open = false">
            
            <div class="flex flex-col gap-1 w-full" :class="(sidebarCollapsed && isDesktop) ? 'items-center' : ''">
                
                {{-- Induk Sistem --}}
                <button @click="
                            if (sidebarCollapsed && isDesktop) {
                                open = !open;
                                $nextTick(() => {
                                    const rect = $el.getBoundingClientRect();
                                    flyoutStyle = `top: ${rect.top}px; left: ${rect.right + 10}px;`;
                                });
                            } else {
                                open = !open;
                            }
                        " 
                        title="Sistem & Pengaturan"
                        class="flex items-center px-3 py-2.5 rounded-xl font-medium text-[13px] transition w-full group focus:outline-none
                        {{ request()->routeIs('company-setting.edit') ? 'bg-blue-50/50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                        :class="(sidebarCollapsed && isDesktop) ? 'justify-center w-11 h-11 px-0' : 'justify-between'">
                    
                    <div class="flex items-center" :class="(sidebarCollapsed && isDesktop) ? 'justify-center' : 'gap-3'">
                        <i data-lucide="settings" class="w-5 h-5 shrink-0 {{ request()->routeIs('company-setting.edit') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        <span x-show="!(sidebarCollapsed && isDesktop)" class="whitespace-nowrap">Sistem</span>
                    </div>
                    <i x-show="!(sidebarCollapsed && isDesktop)" data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                {{-- Sub-Menu Inline (Sidebar Lebar ATAU Mobile Drawer) --}}
                <div x-show="open && !(sidebarCollapsed && isDesktop)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="pl-9 pr-2 py-1 flex flex-col gap-1 mt-1">
                        
                        <a href="{{ route('bookings.export') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition text-gray-500 hover:text-gray-900 hover:bg-gray-50">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 bg-gray-300"></div>
                            Unduh Excel Data
                        </a>
                        <a href="{{ route('company-setting.edit') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition 
                           {{ request()->routeIs('company-setting.edit') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('company-setting.edit') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            Pengaturan Perusahaan
                        </a>
                    </div>
                </div>

                {{-- Flyout Popup (Sidebar Collapsed, DESKTOP SAJA) --}}
                <div x-show="open && (sidebarCollapsed && isDesktop)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-x-1"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     :style="flyoutStyle"
                     class="fixed w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-[100]"
                     x-cloak>
                    <div class="px-4 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sistem</div>
                    <a href="{{ route('bookings.export') }}" 
                       @click="open = false"
                       class="flex items-center gap-3 px-4 py-2.5 text-[13px] transition text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                        <div class="w-1.5 h-1.5 rounded-full shrink-0 bg-gray-300"></div>
                        Unduh Excel Data
                    </a>
                    <a href="{{ route('company-setting.edit') }}" 
                       @click="open = false"
                       class="flex items-center gap-3 px-4 py-2.5 text-[13px] transition
                       {{ request()->routeIs('company-setting.edit') ? 'text-blue-600 font-semibold bg-blue-50/50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('company-setting.edit') ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                        Pengaturan Perusahaan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER SIDEBAR (User Profile & Logout) --}}
    <div class="p-3 border-t border-gray-100 bg-gray-50/50 shrink-0 w-full" :class="(sidebarCollapsed && isDesktop) ? 'flex flex-col items-center gap-2' : ''">
        
        <div class="flex items-center gap-3 px-2 py-2 mb-1" :class="(sidebarCollapsed && isDesktop) ? 'justify-center w-full px-0' : ''">
            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center shrink-0 uppercase">
                {{ substr(Auth::user()->email, 0, 1) }}
            </div>
            <div x-show="!(sidebarCollapsed && isDesktop)" class="min-w-0">
                <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Administrator</p>
                <p class="text-[13px] font-bold text-gray-900 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" 
                    title="Keluar"
                    class="flex items-center text-red-600 bg-white hover:bg-red-50 border border-gray-200 hover:border-red-100 font-semibold text-[13px] transition w-full rounded-xl"
                    :class="(sidebarCollapsed && isDesktop) ? 'justify-center w-11 h-11 mx-auto px-0' : 'px-4 py-2.5 gap-2 justify-center'">
                <i data-lucide="log-out" class="w-4 h-4 shrink-0"></i> 
                <span x-show="!(sidebarCollapsed && isDesktop)">Keluar</span>
            </button>
        </form>
    </div>
</aside>