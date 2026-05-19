{{-- resources/views/components/dashboard/booking-link.blade.php --}}
@php
    $bookingUrl = route('booking.public.show', Auth::id());
@endphp

<div class="bg-[#edf4ff] border border-[#cfe0ff] rounded-[24px] p-4 sm:p-5 overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
        <!-- ICON -->
        <div class="flex items-center gap-2 min-w-fit">
            <i data-lucide="link-2" class="w-5 h-5 text-blue-600"></i>
            <h3 class="text-[16px] font-semibold text-blue-600 whitespace-nowrap">
                Link Booking Klien
            </h3>
        </div>
        <!-- INPUT -->
        <div class="flex-1 min-w-0">
            <input
                type="text"
                readonly
                id="booking-link-input"
                value="{{ $bookingUrl }}"
                class="w-full h-[46px] rounded-2xl border border-[#b8d2ff] bg-white px-4 text-sm text-gray-700 focus:outline-none">
        </div>
        <!-- BUTTON -->
        <button
            id="copy-btn"
            onclick="copyBookingLink()"
            class="h-[46px] px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm flex items-center justify-center gap-2 whitespace-nowrap shrink-0 transition">
            <i data-lucide="copy" class="w-4 h-4"></i>
            Copy Link
        </button>
    </div>
    <p class="text-[13px] text-blue-600 mt-4">
        Kirim link ini ke klien. Data booking yang diisi klien akan masuk ke dashboard admin ini.
    </p>
    <p class="text-[13px] text-gray-500 mt-1 break-all">
        ownerId aktif: {{ Auth::id() }}
    </p>
</div>

<script>
function copyBookingLink() {
    const input = document.getElementById('booking-link-input')
    const btn   = document.getElementById('copy-btn')

    navigator.clipboard.writeText(input.value).then(() => {
        const orig = btn.innerHTML
        btn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Tersalin!
        `
        btn.classList.replace('bg-blue-600', 'bg-green-600')
        btn.classList.replace('hover:bg-blue-700', 'hover:bg-green-700')
        setTimeout(() => {
            btn.innerHTML = orig
            btn.classList.replace('bg-green-600', 'bg-blue-600')
            btn.classList.replace('hover:bg-green-700', 'hover:bg-blue-700')
            if (window.lucide) lucide.createIcons()
        }, 2000)
    }).catch(() => {
        // Fallback untuk browser yang tidak support clipboard API
        input.select()
        document.execCommand('copy')
        input.blur()
    })
}
</script>