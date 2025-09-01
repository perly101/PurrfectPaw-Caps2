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

                    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Edit Doctor: {{ $doctor->full_name }}</h1>

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

        <form action="{{ route('clinic.doctors.update', $doctor->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left column: Basic Info -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-600">*</span></label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $doctor->first_name) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>
                        
                        <div>
                            <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $doctor->middle_name) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-600">*</span></label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $doctor->last_name) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-600">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $doctor->email) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $doctor->phone_number) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                        <select name="gender" id="gender" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $doctor->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $doctor->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="prefer_not_say" {{ old('gender', $doctor->gender) === 'prefer_not_say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="birthday" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" name="birthday" id="birthday" value="{{ old('birthday', $doctor->birthday) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700">Profile Photo</label>
                        @if($doctor->photo)
                        <div class="mt-2 mb-2 flex items-center space-x-4">
                            <img src="{{ asset('storage/' . $doctor->photo) }}" alt="{{ $doctor->full_name }}" class="h-20 w-20 object-cover rounded-full border">
                            <span class="text-sm text-gray-500">Current photo</span>
                        </div>
                        @endif
                        <input type="file" name="photo" id="photo" class="mt-1 block w-full" accept="image/*">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG or JPEG up to 2MB. Leave blank to keep current photo.</p>
                    </div>
                </div>
                
                <!-- Right column: Professional Info -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Professional Information</h2>
                    
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700">Specialization <span class="text-red-600">*</span></label>
                        <input type="text" name="specialization" id="specialization" value="{{ old('specialization', $doctor->specialization) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700">License Number <span class="text-red-600">*</span></label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $doctor->license_number) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700">Years of Experience <span class="text-red-600">*</span></label>
                        <input type="number" name="experience_years" id="experience_years" min="0" value="{{ old('experience_years', $doctor->experience_years) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio / Description</label>
                        <textarea name="bio" id="bio" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('bio', $doctor->bio) }}</textarea>
                        <p class="text-sm text-gray-500">Write a brief description about the doctor's expertise and experience.</p>
                    </div>
                    
                    <div class="border-t pt-4 mt-6">
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Account Status</h2>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Availability Status</label>
                            <div class="flex items-center space-x-4">
                                <select name="availability_status" class="focus:ring-indigo-500 focus:border-indigo-500 shadow-sm sm:text-sm border-gray-300 rounded-md
                                    {{ $doctor->availability_status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($doctor->availability_status === 'on_leave' ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-red-100 text-red-800') }}">
                                    <option value="active" {{ $doctor->availability_status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="on_leave" {{ $doctor->availability_status === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                    <option value="not_accepting" {{ $doctor->availability_status === 'not_accepting' ? 'selected' : '' }}>Not Accepting Appointments</option>
                                </select>
                            </div>
                        </div>
                        
                        @if($doctor->user)
                        <div class="mt-4 text-sm">
                            <p class="font-medium">User Account: <span class="text-green-600">Linked</span></p>
                            <p class="text-gray-600">Email: {{ $doctor->user->email }}</p>
                            <p class="text-gray-600">Role: {{ ucfirst($doctor->user->role) }}</p>
                            <p class="text-gray-500 text-xs mt-2">
                                Note: Changes to name, email, phone number, gender and birthday will also be applied to the linked user account.
                            </p>
                        </div>
                        @else
                        <div class="mt-4 text-sm">
                            <p class="font-medium text-red-600">User Account: Not Linked</p>
                            <p class="text-gray-500">This doctor doesn't have an associated user account.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="mt-8 border-t pt-6 flex justify-end">
                <a href="{{ route('clinic.doctors.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Doctor
                </button>
            </div>
        </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
