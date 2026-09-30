<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun Tester</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Buat Akun Admin (Testing)</h1>
            <p class="text-sm text-gray-500">Halaman khusus sementara untuk Usability Testing. Hapus halaman ini jika pengujian sudah selesai.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 mb-6 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/buat-akun-tester" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="admin@tester.com" 
                    class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter" 
                    class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-blue-500 transition shadow-sm">
            </div>

            <button type="submit" class="w-full h-11 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition mt-4 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Buat Akun & Login
            </button>
        </form>
    </div>
</body>
</html>
