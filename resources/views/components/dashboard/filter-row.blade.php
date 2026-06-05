{{-- resources/views/components/dashboard/filter-row.blade.php --}}
{{-- 
    [PENJELASAN UNTUK SIDANG]
    (Reusable UI Component) Komponen ini dirancang berdasarkan prinsip UCD 
    untuk memastikan konsistensi tata letak (layout) antara label dan input filter 
    di berbagai ukuran layar (Responsif).
--}}
<div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
    <div class="w-full sm:w-[110px] sm:flex-shrink-0">
        <p class="text-[14px] font-medium text-gray-700 whitespace-nowrap">
            {{ $label }}:
        </p>
    </div>

    <div class="flex-1 min-w-0">
        {{ $slot }}
    </div>
</div>