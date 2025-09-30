<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Thanks for signing up! To complete your registration, please enter the 6-digit verification code we just sent to your email address.') }}
        </div>

        <!-- Session Status -->
        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification code has been sent to your email address.') }}
            </div>
        @endif

        <!-- Session Error -->
        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('verification.otp.verify') }}">
            @csrf

            <!-- OTP Code -->
            <div>
                <x-label for="otp" :value="__('Verification Code')" />
                <x-input id="otp" class="block mt-1 w-full" type="text" name="otp" :value="old('otp')" required autofocus maxlength="6" placeholder="Enter 6-digit code" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Verify Email') }}
                </x-button>
            </div>
        </form>

        <div class="mt-6">
            <p class="text-sm text-gray-600">
                {{ __("Didn't receive the code?") }}
            </p>

            <form method="POST" action="{{ route('verification.otp.resend') }}" class="mt-2">
                @csrf

                <div>
                    <x-button>
                        {{ __('Resend Code') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>