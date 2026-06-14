{{-- resources/views/components/dashboard/booking-link.blade.php --}}
{{-- 
    [PENJELASAN UNTUK SIDANG]
    Komponen ini bertindak sebagai titik mulai (Entry Point) bagi pelanggan. 
    Dengan menyalin tautan (link) publik ini, pelanggan dapat menginput data pesanan (Input TPS),
    yang nantinya akan diolah (Process) secara otomatis di Dashboard Admin.
--}}
@php
    $bookingUrl = route('booking.public.show', Auth::id());
@endphp

<div class="bg-[#edf4ff] border border-[#cfe0ff] rounded-[24px] p-4 sm:p-5 overflow-hidden">
    {{-- Header label --}}
    <div class="flex items-center gap-2 mb-3">
        <i data-lucide="link-2" class="w-5 h-5 text-blue-600 shrink-0"></i>
        <h3 class="text-[16px] font-semibold text-blue-600">
            Link Booking Klien
        </h3>
    </div>

    {{-- URL input (full width, always readable) --}}
    <input
        type="text"
        readonly
        id="booking-link-input"
        value="{{ $bookingUrl }}"
        class="w-full h-[42px] rounded-2xl border border-[#b8d2ff] bg-white px-4 text-sm text-gray-700 focus:outline-none mb-3">

    {{-- Copy button (full width below input) --}}
    <button
        id="copy-btn"
        onclick="copyBookingLink()"
        class="w-full h-[42px] rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm flex items-center justify-center gap-2 transition">
        <i data-lucide="copy" class="w-4 h-4"></i>
        Copy Link
    </button>

    <p class="text-[13px] text-blue-600 mt-3">
        Kirim link ini ke klien. Data booking yang diisi klien akan masuk ke dashboard admin ini.
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
        input.select()
        document.execCommand('copy')
        input.blur()
    })
}
</script>