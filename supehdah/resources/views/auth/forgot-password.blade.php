<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <div class="flex flex-col items-center">
                    <img src="{{ asset('images/pet-logo.png') }}" alt="PurrfectPaw Logo" class="w-24 h-24 rounded-full object-cover border-2 border-indigo-300">
                    <span class="mt-2 text-2xl font-bold text-purple-600">PurrfectPaw</span>
                </div>
            </a>
        </x-slot>

        <div class="mb-6 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Please enter your registered email address and we will send you a password reset link to create a new password.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Your Email Address')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="Enter your registered email" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="text-sm text-purple-500 hover:text-purple-700" href="{{ route('login') }}">
                    {{ __('Back to Login') }}
                </a>
                
                <x-button class="ml-4 bg-purple-500 hover:bg-purple-600">
                    {{ __('Send Reset Link') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
