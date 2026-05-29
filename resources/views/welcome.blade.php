<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rozi Photography</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-100 min-h-screen font-sans antialiased">

    <section class="flex flex-col items-center justify-center pt-24 pb-20 px-6">

        <h1 class="text-6xl font-bold text-blue-600 text-center">
            Rozi Photography
        </h1>

        <p class="mt-6 text-gray-600 text-center max-w-2xl text-lg leading-relaxed">
            Sistem pencatatan klien dan appointment yang sederhana dan efisien.
            Kelola jadwal, lacak status, dan tingkatkan produktivitas tim Anda.
        </p>

        <a href="{{ route('login') }}"
           class="mt-10 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-10 py-4 rounded-2xl shadow-lg transition duration-300">
            Login
        </a>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-20 w-full max-w-6xl">

            <div class="bg-white rounded-2xl shadow-md p-8 flex flex-col items-center text-center hover:shadow-xl transition duration-300 cursor-pointer">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-5">
                    <i data-lucide="calendar-days" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Tampilan Kalender
                </h3>
                <p class="text-gray-500 text-sm mt-2">
                    Kelola jadwal appointment dengan tampilan yang rapi dan mudah dipahami.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-8 flex flex-col items-center text-center hover:shadow-xl transition duration-300 cursor-pointer">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mb-5">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Manajemen Klien
                </h3>
                <p class="text-gray-500 text-sm mt-2">
                    Simpan dan kelola data klien secara terstruktur dan efisien.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-8 flex flex-col items-center text-center hover:shadow-xl transition duration-300 cursor-pointer">
                <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center mb-5">
                    <i data-lucide="clock-3" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Status Tracking
                </h3>
                <p class="text-gray-500 text-sm mt-2">
                    Pantau status booking dan progress pekerjaan secara realtime.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-8 flex flex-col items-center text-center hover:shadow-xl transition duration-300 cursor-pointer">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mb-5">
                    <i data-lucide="shield-check" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Data Aman
                </h3>
                <p class="text-gray-500 text-sm mt-2">
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