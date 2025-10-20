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

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Create a new, secure password for your account.') }}
        </div>
        
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required readonly />
                <p class="text-xs text-gray-500 mt-1">This is the email address that will be updated.</p>
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('New Password')" />

                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required placeholder="Enter your new password" />
                <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters</p>
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm New Password')" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation" required placeholder="Confirm your new password" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-button class="bg-purple-500 hover:bg-purple-600">
                    {{ __('Reset Password') }}
                </x-button>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Reset Password') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
