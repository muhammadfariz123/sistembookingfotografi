<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rozi Photography</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900 overflow-hidden h-screen">

    <section class="h-full flex flex-col items-center justify-center p-6 max-w-7xl mx-auto gap-8 lg:gap-14">

        <div class="flex flex-col items-center text-center">
            
            <h1 class="text-[40px] md:text-[56px] lg:text-[64px] font-extrabold text-blue-600 tracking-tight leading-none">
                Rozi Photography
            </h1>

            <p class="mt-4 text-[16px] md:text-[18px] text-gray-600 max-w-2xl leading-relaxed">
                Sistem pencatatan klien dan appointment yang sederhana dan efisien.
                Kelola jadwal, lacak status, dan tingkatkan produktivitas tim Anda.
            </p>

            <a href="{{ route('login') }}"
               class="mt-8 h-[56px] px-10 bg-blue-600 hover:bg-blue-700 text-white text-[16px] font-semibold rounded-2xl shadow-sm flex items-center justify-center transition-colors">
                Login
            </a>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="calendar-days" class="w-7 h-7"></i>
                </div>
                <h3 class="text-[18px] font-bold text-gray-900">
                    Tampilan Kalender
                </h3>
                <p class="text-[14px] text-gray-500 mt-2 leading-relaxed">
                    Kelola jadwal appointment dengan tampilan yang rapi dan mudah dipahami.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="users" class="w-7 h-7"></i>
                </div>
                <h3 class="text-[18px] font-bold text-gray-900">
                    Manajemen Klien
                </h3>
                <p class="text-[14px] text-gray-500 mt-2 leading-relaxed">
                    Simpan dan kelola data klien secara terstruktur dan efisien.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-yellow-100 text-yellow-600 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="clock-3" class="w-7 h-7"></i>
                </div>
                <h3 class="text-[18px] font-bold text-gray-900">
                    Status Tracking
                </h3>
                <p class="text-[14px] text-gray-500 mt-2 leading-relaxed">
                    Pantau status booking dan progress pekerjaan secara realtime.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mb-4">
                    <i data-lucide="shield-check" class="w-7 h-7"></i>
                </div>
                <h3 class="text-[18px] font-bold text-gray-900">
                    Data Aman
                </h3>
                <p class="text-[14px] text-gray-500 mt-2 leading-relaxed">
                    Data tersimpan aman dengan sistem autentikasi admin internal.
                </p>
            </div>

        </div>

    </section>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>