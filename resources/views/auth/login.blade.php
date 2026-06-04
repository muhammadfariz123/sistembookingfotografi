{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>

    <div x-data="loginForm()" class="w-full">

        <div x-show="success" x-cloak x-transition
            class="fixed top-5 right-5 bg-green-500 text-white px-5 py-4 rounded-2xl shadow-xl flex items-center gap-3 z-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="font-medium text-[14px]">Login berhasil, mengalihkan...</span>
        </div>

        <div x-show="error" x-cloak x-transition
            class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-[13px] font-medium shadow-sm text-center">
            Email atau password yang Anda masukkan salah.
        </div>

        <div class="text-center mb-8">
            <h1 class="text-[26px] font-bold text-blue-600 tracking-tight">
                Login Admin
            </h1>
            <p class="text-gray-500 text-[14px] mt-1.5">
                Silakan login untuk masuk ke dashboard.
            </p>
        </div>

        <form @submit.prevent="submitLogin">
            @csrf

            <div>
                <label class="block text-[13px] font-semibold text-gray-700 mb-2">Email</label>
                <input x-model="email" @input="error = false" type="email" required placeholder="admin@gmail.com"
                    class="w-full h-[46px] rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all shadow-sm px-4 text-[14px] outline-none">
            </div>

            <div class="mt-5">
                <label class="block text-[13px] font-semibold text-gray-700 mb-2">Password</label>
                <input x-model="password" @input="error = false" type="password" required placeholder="••••••••"
                    class="w-full h-[46px] rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all shadow-sm px-4 text-[14px] outline-none">
            </div>

            <div class="mt-8">
                <button type="submit" :disabled="loading"
                    class="w-full h-[46px] bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 text-[15px]">
                    
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>

                    <span x-text="loading ? 'Memverifikasi...' : 'Login'"></span>
                </button>
            </div>
        </form>

    </div>

    <script>
        function loginForm() {
            return {
                email: '',
                password: '',
                loading: false,
                success: false,
                error: false,

                async submitLogin() {
                    this.loading = true
                    this.error = false

                    try {
                        const response = await fetch("{{ route('login') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                email: this.email,
                                password: this.password
                            })
                        })

                        if (response.ok) {
                            const data = await response.json()
                            this.success = true
                            setTimeout(() => window.location.href = data.redirect, 1000)
                        } else {
                            this.error = true
                        }
                    } catch (e) {
                        this.error = true
                    } finally {
                        this.loading = false
                    }
                }
            }
        }
    </script>
</x-guest-layout>