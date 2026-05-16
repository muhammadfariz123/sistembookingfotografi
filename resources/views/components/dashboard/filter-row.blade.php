<div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">

    <!-- LABEL -->
    <div class="w-full sm:w-[110px] sm:flex-shrink-0">

        <p class="text-[14px] font-medium text-gray-700 whitespace-nowrap">
            {{ $label }}:
        </p>

    </div>

    <!-- CONTENT -->
    <div class="flex-1 min-w-0">

        {{ $slot }}

    </div>

</div>