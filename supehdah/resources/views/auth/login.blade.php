<!-- resources/views/auth/login.blade.php -->
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#E8C0D8] via-[#DEB1A8] to-[#C2AA6A] p-6">
        <div class="bg-white shadow-xl rounded-2xl flex flex-col md:flex-row overflow-hidden max-w-4xl w-full animate-fade-in">
            
            <!-- Left (Login Form) -->
            <div class="w-full md:w-1/2 p-8">
                <div class="flex justify-center mb-1">
                    <a href="/">
                        <img src="/images/pet-logo.png" alt="SuPehDah Logo" class="w-[120px] h-[120px] object-contain">
                    </a>
                </div>

                <h1 class="text-center text-2xl font-bold text-[#1C5B38] mb-1">PurrfectPaw</h1>
                <p class="text-center text-gray-600 text-sm mb-6">Login to your account</p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-label for="email" :value="__('Email')" class="text-[#3D7256]" />
                        <x-input id="email" class="block mt-1 w-full border-[#C2AA6A] focus:ring-[#1C5B38] focus:border-[#1C5B38]" type="email" name="email" :value="old('email')" required autofocus />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-label for="password" :value="__('Password')" class="text-[#3D7256]" />
                        <x-input id="password" class="block mt-1 w-full border-[#C2AA6A] focus:ring-[#1C5B38] focus:border-[#1C5B38]" type="password" name="password" required autocomplete="current-password" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#3D7256] shadow-sm focus:ring-[#1C5B38]" name="remember">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-between mt-6">
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-[#1C5B38] hover:text-[#09371F]" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        <x-button class="ml-3 bg-[#C2AA6A] hover:bg-[#b69a5f] text-white">
                            {{ __('Log in') }}
                        </x-button>
                    </div>
                </form>
                
                <!-- Social Login Separator -->
                <div class="my-6 flex items-center">
                    <hr class="flex-grow border-t border-gray-300">
                    <span class="px-3 text-sm text-gray-500">or</span>
                    <hr class="flex-grow border-t border-gray-300">
                </div>
                
                <!-- Google Login Button -->
                <a href="{{ route('auth.google') }}" class="flex items-center justify-center w-full py-2.5 border border-gray-300 rounded-lg shadow-sm bg-white hover:bg-gray-50 transition duration-150">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                    </svg>
                    <span class="text-gray-700 font-medium">Sign in with Google</span>
                </a>
            </div>

            <!-- Right (Image/Design) -->
            <div class="hidden md:flex w-1/2 relative">
                <img src="/images/welcome2.jpg" alt="Side Illustration" class="w-full h-full object-cover brightness-90">
                <!-- <div class="absolute bottom-6 left-6 bg-[#1C5B38]/80 px-4 py-2 rounded-lg">
                    <span class="text-white text-xl font-semibold tracking-wide">SuPehDah</span> -->
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
