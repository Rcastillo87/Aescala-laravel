<x-guest-layout>
    <div class="flex shadow-md">
        <div class="flex flex-wrap relative content-center justify-center rounded-l-md bg-white w-96 h-[32rem] md:shadow-[40px_0px_30px_rgb(255,255,255)]">
            <div class="w-72">
                <img src="{{ asset('img/logo.png') }}" class="absolute top-6 right-5 w-20" alt="logo">
                <h1 class="text-3xl font-bold cursor-pointer">Iniciar Sesión</h1>
                <small class="text-gray-600">¡Bienvenido!, ingresa tus credenciales para poder continuar</small>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Correo')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Contraseña')" />

                        <x-text-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        :value="old('password')"
                                        required autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                                {{ __('Olvidaste Tu contraseña?') }}
                            </a>
                        @endif

                        <x-primary-button class="ms-3">
                            {{ __('Login') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
        <div class="md:flex flex-wrap content-center justify-center rounded-r-md hidden w-96 h-[32rem]">
            <img src="{{ asset('img/banner.jpg') }}" class="w-full h-full bg-center bg-no-repeat bg-cover rounded-r-md object-cover" alt="logo">
        </div>
    </div>
    <script>
        async function refreshCsrfToken() {
            try {
                const response = await fetch("{{ route('csrf.refresh') }}", {
                    credentials: 'include'
                });
                const data = await response.json();
                if (data.token) {
                    const tokenInput = document.querySelector('form input[name="_token"]');
                    if (tokenInput) tokenInput.value = data.token;
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) meta.setAttribute('content', data.token);
                }
            } catch (e) {
                console.warn('No se pudo renovar el CSRF token:', e);
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('visibilitychange', async function () {
                if (document.visibilityState === 'visible') {
                    await refreshCsrfToken();
                }
            });
        });
    </script>
</x-guest-layout>
