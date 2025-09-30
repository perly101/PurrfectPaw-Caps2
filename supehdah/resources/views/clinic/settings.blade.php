@php
    use App\Models\ClinicInfo;
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    {{-- Include mobile navigation (only visible on mobile) --}}
    @include('clinic.components.mobile-nav')

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:space-x-6">
                
                {{-- Sidebar (hidden on mobile) --}}
                <div class="hidden md:block md:w-1/4 mb-6 md:mb-0">
                    @include('clinic.components.sidebar')
                </div>

                <div class="w-full md:flex-1 mt-16 md:mt-0">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-4 sm:mb-6">Account Settings</h1>

                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 sm:p-4 mb-4 sm:mb-6 text-sm sm:text-base" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-4 sm:mb-6">
            <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Profile Information</h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Update your clinic's profile information and contact details.</p>
            </div>
            <div class="p-4 sm:p-6">
                <form action="{{ route('clinic.settings.update-profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Profile Picture -->
                        <div class="md:col-span-2 flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                            <div class="shrink-0">
                                <img class="h-20 w-20 sm:h-24 sm:w-24 object-cover rounded-full border-2 border-gray-200" 
                                    src="{{ asset('storage/' . $clinic->profile_picture) }}" 
                                    alt="Current profile photo">
                            </div>
                            <label class="block w-full">
                                <span class="sr-only">Choose profile photo</span>
                                <input type="file" name="profile_picture" 
                                    class="block w-full text-xs sm:text-sm text-gray-500 file:mr-2 sm:file:mr-4 file:py-1 sm:file:py-2 file:px-3 sm:file:px-4 file:rounded-md file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                @error('profile_picture')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </label>
                        </div>

                        <!-- Admin Name -->
                        <!-- <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Admin Name</label>
                            <input type="text" name="name" id="name" value="{{ $user->name }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div> -->

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ $user->email }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs sm:text-sm py-1.5 sm:py-2">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Clinic Name -->
                        <div>
                            <label for="clinic_name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Clinic Name</label>
                            <input type="text" name="clinic_name" id="clinic_name" value="{{ $clinic->clinic_name }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs sm:text-sm py-1.5 sm:py-2">
                            @error('clinic_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Number -->
                        <div>
                            <label for="contact_number" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="text" name="contact_number" id="contact_number" value="{{ $clinic->contact_number }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs sm:text-sm py-1.5 sm:py-2">
                            @error('contact_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="address" id="address" value="{{ $clinic->address }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs sm:text-sm py-1.5 sm:py-2">
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 sm:mt-6">
                        <button type="submit" class="px-3 sm:px-4 py-1.5 sm:py-2 bg-blue-600 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            Save Profile Information
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Update Password</h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Ensure your account is using a secure password.</p>
            </div>
            <div class="p-4 sm:p-6">
                <form action="{{ route('clinic.settings.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-3 sm:space-y-4">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" name="current_password" id="current_password"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs sm:text-sm py-1.5 sm:py-2">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password" id="password"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs sm:text-sm py-1.5 sm:py-2">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs sm:text-sm py-1.5 sm:py-2">
                        </div>
                    </div>

                    <div class="mt-4 sm:mt-6">
                        <button type="submit" class="px-3 sm:px-4 py-1.5 sm:py-2 bg-blue-600 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
            </div>
            </div>
        </div>
    </div>
</x-app-layout>
