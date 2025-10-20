    {{-- resources/views/clinic/register/step1.blade.php --}}

<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center">
        <div class="sm:max-w-xl md:max-w-2xl lg:max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Register Your Clinic</h2>
                    <p class="text-gray-500 text-xs md:text-sm mt-1">Step 1: Clinic Information</p>
                </div>
                
                <div class="flex items-center gap-1 sm:space-x-2">
                    <span class="flex h-6 w-6 sm:h-8 sm:w-8 items-center justify-center rounded-full border-2 border-indigo-600 bg-indigo-600 text-white text-xs sm:text-sm">1</span>
                    <span class="h-0.5 w-6 sm:w-8 bg-gray-300"></span>
                    <span class="flex h-6 w-6 sm:h-8 sm:w-8 items-center justify-center rounded-full border-2 border-gray-300 text-gray-400 text-xs sm:text-sm">2</span>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 md:p-8 border border-gray-200">
                <!-- Selected Plan Section -->
                <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4 relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-indigo-600 text-white text-xs px-2 py-1 rounded-bl-md">
                        Selected Plan
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $planDetails['plan_name'] }}</h3>
                    <p class="text-indigo-600 text-xl font-bold mt-1">
                        ₱{{ number_format($planDetails['amount']) }}
                        <span class="text-sm text-gray-500 font-normal">/ {{ $planDetails['billing_cycle'] }}</span>
                    </p>
                    <p class="text-gray-600 text-sm mt-2">
                        @if($planDetails['plan_type'] === 'yearly')
                        Save ₱20,000 compared to monthly billing
                        @else
                        Flexible monthly billing with no long-term commitment
                        @endif
                    </p>
                </div>

                <form action="{{ route('clinic.register.step1.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <!-- Hidden fields for plan details -->
                    <input type="hidden" name="plan_type" value="{{ $planDetails['plan_type'] }}">
                    <input type="hidden" name="amount" value="{{ $planDetails['amount'] }}">
                    <input type="hidden" name="billing_cycle" value="{{ $planDetails['billing_cycle'] }}">
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-3 pb-2 border-b">Clinic Details</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Clinic Name
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email field has been removed as requested -->

                            <div>
                                <label class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Address
                                </label>
                                <input type="text" name="address" value="{{ old('address') }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" required>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Contact Number
                                </label>
                                <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" required>
                                @error('contact_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-3 pb-2 border-b">Clinic Branding</h3>
                        
                        <div>
                            <label class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Clinic Logo (optional)
                            </label>
                            <div class="mt-1 flex flex-col sm:flex-row sm:items-center gap-3">
                                <span class="inline-block h-16 w-16 rounded-lg overflow-hidden bg-gray-100 border border-gray-300">
                                    <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                                <input type="file" name="logo" class="sm:ml-5 rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none transition">
                                @error('logo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="text-gray-500 text-xs mt-2">Recommended size: 400x400 pixels. Maximum file size: 2MB.</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t mt-6">
                        <a href="{{ route('landing') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Plans
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium shadow-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-400 transition flex items-center text-sm">
                            Continue to Step 2
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>