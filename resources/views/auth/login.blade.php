<x-guest-layout>

    <div x-data="loginForm()" class="w-full max-w-md">

        <!-- SUCCESS -->
        <div
            x-show="success"
            x-transition
            class="fixed top-5 right-5 bg-green-500 text-white px-5 py-4 rounded-2xl shadow-xl flex items-center gap-3 z-50">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"/>

            </svg>

            <span>Login berhasil...</span>

        </div>

        <!-- ERROR -->
        <div
            x-show="error"
            x-transition
            class="mb-5 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl text-sm shadow-sm">

            Email atau password salah

        </div>

        <!-- TITLE -->
        <div class="text-center mb-8">

            <h1 class="text-3xl font-bold text-blue-600">
                Login Admin
            </h1>

            <p class="text-gray-500 mt-2">
                Silakan login untuk masuk ke dashboard admin.
            </p>

        </div>

        <!-- FORM -->
        <form @submit.prevent="submitLogin">

            @csrf

            <!-- EMAIL -->
            <div>

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>

                <input
                    x-model="email"
                    @input="error = false"
                    type="email"
                    required
                    class="w-full rounded-2xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-3">

            </div>

            <!-- PASSWORD -->
            <div class="mt-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>

                <input
                    x-model="password"
                    @input="error = false"
                    type="password"
                    required
                    class="w-full rounded-2xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm px-4 py-3">

            </div>

            <!-- BUTTON -->
            <div class="mt-8">

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold py-3 rounded-2xl transition flex items-center justify-center gap-2">

                    <svg
                        x-show="loading"
                        class="animate-spin h-5 w-5"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24">

                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"/>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>

                    </svg>

                    <span x-text="loading ? 'Loading...' : 'Login'"></span>

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

                    const response = await fetch("{{ route('login') }}", {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },

                        body: JSON.stringify({
                            email: this.email,
                            password: this.password
                        })

                    })

                    if (response.ok) {

                        const data = await response.json()

                        this.loading = false
                        this.success = true

                        setTimeout(() => {

                            window.location.href = data.redirect

                        }, 1500)

                    } else {

                        this.loading = false
                        this.error = true

                    }

                }

            }

        }

    </script>

</x-guest-layout>