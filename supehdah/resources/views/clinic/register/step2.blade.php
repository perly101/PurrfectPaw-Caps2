{{-- resources/views/clinic/register/step2.blade.php --}}

<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center">
        <div class="sm:max-w-xl md:max-w-2xl lg:max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Register Your Clinic</h2>
                    <p class="text-gray-500 text-xs md:text-sm mt-1">Step 2: Staff Account Setup</p>
                </div>
                
                <div class="flex items-center gap-1 sm:space-x-2">
                    <span class="flex h-6 w-6 sm:h-8 sm:w-8 items-center justify-center rounded-full border-2 border-gray-300 text-gray-400 text-xs sm:text-sm">1</span>
                    <span class="h-0.5 w-6 sm:w-8 bg-indigo-600"></span>
                    <span class="flex h-6 w-6 sm:h-8 sm:w-8 items-center justify-center rounded-full border-2 border-indigo-600 bg-indigo-600 text-white text-xs sm:text-sm">2</span>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6 md:p-8 border border-gray-200">
                <!-- Validation Errors -->
                @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm">
                    <div class="font-medium">Whoops! Something went wrong.</div>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- Selected Plan Section -->
                <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4 relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-indigo-600 text-white text-xs px-2 py-1 rounded-bl-md">
                        Selected Plan
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $clinicData['plan_type'] === 'monthly' ? 'Monthly Plan' : 'Annual Plan' }}</h3>
                    <p class="text-indigo-600 text-xl font-bold mt-1">
                        ₱{{ number_format($clinicData['amount']) }}
                        <span class="text-sm text-gray-500 font-normal">/ {{ $clinicData['plan_type'] === 'monthly' ? 'month' : 'year' }}</span>
                    </p>
                    <p class="text-gray-600 text-sm mt-2">
                        Clinic: {{ $clinicData['name'] }}
                    </p>
                </div>

                <form method="POST" action="{{ route('clinic.register.step2.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-3 pb-2 border-b">Staff Personal Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    First Name
                                </label>
                                <input id="first_name" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus />
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <label for="middle_name" class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Middle Name (Optional)
                                </label>
                                <input id="middle_name" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" type="text" name="middle_name" value="{{ old('middle_name') }}" />
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Last Name
                                </label>
                                <input id="last_name" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" type="text" name="last_name" value="{{ old('last_name') }}" required />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-3 pb-2 border-b">Contact Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Email Address -->
                            <div>
                                <label for="email" class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Email
                                </label>
                                <input id="email" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" type="email" name="email" value="{{ old('email') }}" required />
                            </div>
                            
                            <!-- Phone Number -->
                            <div>
                                <label for="phone_number" class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Phone Number
                                </label>
                                <input id="phone_number" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" type="text" name="phone_number" value="{{ old('phone_number') }}" />
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-3 pb-2 border-b">Personal Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Gender -->
                            <div>
                                <label for="gender" class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Gender
                                </label>
                                <select id="gender" name="gender" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="prefer_not_say" {{ old('gender') == 'prefer_not_say' ? 'selected' : '' }}>Prefer not to say</option>
                                </select>
                            </div>

                            <!-- Birthday -->
                            <div>
                                <label for="birthday" class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Birthday
                                </label>
                                <input id="birthday" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" type="date" name="birthday" value="{{ old('birthday') }}" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-3 pb-2 border-b">Account Security</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Password -->
                            <div>
                                <label for="password" class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Password
                                </label>
                                <input id="password" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" 
                                       type="password" 
                                       name="password" 
                                       required autocomplete="new-password" />
                                <p class="text-gray-500 text-xs mt-1">Password must be at least 8 characters and include letters and numbers.</p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="font-medium text-sm text-gray-700 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Confirm Password
                                </label>
                                <input id="password_confirmation" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" 
                                       type="password" 
                                       name="password_confirmation" 
                                       required />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t mt-6">
                        <a href="{{ route('clinic.register.select-plan', ['plan' => $clinicData['plan_type']]) }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Step 1
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium shadow-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-400 transition flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Complete Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Optional: Add client-side password validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const hasLetter = /[a-zA-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const isLongEnough = password.length >= 8;
            
            if (password && (!hasLetter || !hasNumber || !isLongEnough)) {
                this.classList.add('border-yellow-400');
                this.classList.add('bg-yellow-50');
            } else {
                this.classList.remove('border-yellow-400');
                this.classList.remove('bg-yellow-50');
            }
        });
    </script>
</x-app-layout>