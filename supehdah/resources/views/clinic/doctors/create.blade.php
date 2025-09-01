@php
    use App\Models\ClinicInfo;
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">
            
            {{-- Sidebar --}}
            <div class="w-1/4">
                @include('clinic.components.sidebar')
            </div>
            
            {{-- Main Content --}}
            <div class="w-3/4">
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <div class="mb-6">
                        <a href="{{ route('clinic.doctors.index') }}" class="flex items-center text-indigo-600 hover:text-indigo-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Doctors List
                        </a>
                    </div>

                    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Add New Doctor</h1>

        @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">Please correct the following errors:</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('clinic.doctors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left column: Basic Info -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-600">*</span></label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>
                        
                        <div>
                            <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-600">*</span></label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-600">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                        <select name="gender" id="gender" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="prefer_not_say" {{ old('gender') === 'prefer_not_say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="birthday" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" name="birthday" id="birthday" value="{{ old('birthday') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700">Profile Photo</label>
                        <input type="file" name="photo" id="photo" class="mt-1 block w-full" accept="image/*">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG or JPEG up to 2MB</p>
                    </div>
                </div>
                
                <!-- Right column: Professional Info & User Account -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Professional Information</h2>
                    
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700">Specialization <span class="text-red-600">*</span></label>
                        <input type="text" name="specialization" id="specialization" value="{{ old('specialization') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700">License Number <span class="text-red-600">*</span></label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700">Years of Experience <span class="text-red-600">*</span></label>
                        <input type="number" name="experience_years" id="experience_years" min="0" value="{{ old('experience_years', 0) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio / Description</label>
                        <textarea name="bio" id="bio" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('bio') }}</textarea>
                        <p class="text-sm text-gray-500">Write a brief description about the doctor's expertise and experience.</p>
                    </div>
                    
                    <div class="border-t pt-4 mt-6">
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2">User Account Details</h2>
                        <p class="text-sm text-gray-500 mb-4">A user account will be created automatically for this doctor.</p>
                        
                        <div>
                            <label for="temp_password" class="block text-sm font-medium text-gray-700">Temporary Password <span class="text-red-600">*</span></label>
                            <input type="text" name="temp_password" id="temp_password" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                            <p class="mt-1 text-xs text-gray-500">Provide a temporary password for the doctor to log in with. They can change it later.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 border-t pt-6 flex justify-end">
                <a href="{{ route('clinic.doctors.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Doctor
                </button>
            </div>
        </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
