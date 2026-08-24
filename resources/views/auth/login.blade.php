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
                <div class="relative">
                    <input x-model="password" @input="error = false" :type="showPassword ? 'text' : 'password'" required placeholder="••••••••"
                        class="w-full h-[46px] rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all shadow-sm px-4 pr-12 text-[14px] outline-none">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-cloak x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
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
                showPassword: false,
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
                            // Dioptimalkan: Langsung alihkan tanpa jeda waktu lama
                            window.location.href = data.redirect
                        } else {
                            this.error = true
                            this.loading = false
                        }
                    } catch (e) {
                        this.error = true
                        this.loading = false
                    }
                }
            }
        }
    </script>
</x-guest-layout>