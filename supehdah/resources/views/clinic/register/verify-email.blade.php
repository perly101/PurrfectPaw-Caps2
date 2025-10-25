{{-- resources/views/clinic/register/verify-email.blade.php --}}

<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center">
        <div class="sm:max-w-xl md:max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Verify Your Email Address</h2>
                <p class="text-gray-500 text-base md:text-lg mt-3">
                    We've sent a verification code to <span class="font-semibold">{{ $email }}</span>
                </p>
            </div>
            
            <div class="bg-white shadow-lg rounded-xl p-6 md:p-8 border border-gray-200">
                <!-- Session Status -->
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ __('A new verification code has been sent to your email address.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Error Message -->
                @if(session('error'))
                    <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    {{ __('Whoops! Something went wrong.') }}
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- OTP Display removed - emails are now working correctly -->
                <!-- Display only in local/development environment for testing -->
                @if(session('dev_otp') && app()->environment(['local', 'development', 'testing']))
                    <div class="mb-4 p-4 rounded-md bg-yellow-50 border border-yellow-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Development Mode: OTP Code
                                </h3>
                                <div class="mt-2 text-md text-yellow-700 font-bold">
                                    {{ session('dev_otp') }}
                                </div>
                                <p class="text-xs text-yellow-600 mt-1">This code is only shown in development environments.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="text-center mb-6">
                    <p class="text-gray-600">Please enter the 6-digit verification code we sent to your email</p>
                </div>

                <form method="POST" action="{{ route('clinic.register.verify') }}" class="space-y-6">
                    @csrf

                    <div class="flex flex-col items-center space-y-4">
                        <div class="w-full max-w-xs">
                            <label for="otp" class="sr-only">Verification Code</label>
                            <input
                                id="otp"
                                name="otp"
                                type="text"
                                maxlength="6"
                                class="block w-full text-center py-3 px-4 border border-gray-300 rounded-lg text-2xl tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="000000"
                                required
                                autocomplete="off"
                                autofocus
                            >
                            <p class="text-xs text-gray-500 mt-2 text-center">Enter the 6-digit code</p>
                        </div>
                        
                        <button type="submit" class="w-full max-w-xs py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Verify Email
                        </button>
                    </div>
                </form>

                <div class="mt-8 border-t border-gray-200 pt-6 text-center">
                    <p class="text-gray-600 text-sm">Didn't receive the code?</p>
                    <form method="POST" action="{{ route('clinic.register.resend') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium text-sm focus:outline-none">
                            Resend Code
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">Need help? <a href="#" class="text-blue-600 hover:text-blue-800">Contact our support team</a></p>
            </div>
        </div>
    </div>
</x-app-layout>