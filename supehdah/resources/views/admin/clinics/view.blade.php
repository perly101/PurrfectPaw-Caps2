<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        {{-- Sidebar (direct include) --}}
        @include('admin.components.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 p-6 ml-64">
            <div class="bg-white shadow-lg rounded-xl px-8 pt-6 pb-8 mb-4">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Clinic Details</h2>
                        <p class="text-gray-500 text-sm mt-1">Manage clinic information and associated account</p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.clinics') }}" class="flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to List
                        </a>
                        
                        <form action="{{ route('admin.clinics.delete', $clinic->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this clinic? This will also delete the associated user account.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Clinic
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex items-center mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="p-2 bg-blue-100 rounded-md mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <form action="{{ route('admin.clinic.download', $clinic->id) }}" method="GET" class="flex flex-1 items-center" target="_blank">
                        <div class="mr-3 flex-1">
                            <label class="block text-sm font-medium text-blue-700 mb-1">Password of the account</label>
                            <input type="text" name="password" placeholder="Enter password of the account" 
                                class="border border-blue-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-blue-200 focus:border-blue-400" required>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </button>
                    </form>
                </div>

                {{-- Notification Messages --}}
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Update Clinic Details --}}
                <div class="bg-white p-6 rounded-lg border border-gray-200 mb-8 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1v1a1 1 0 11-2 0v-1H7v1a1 1 0 11-2 0v-1a1 1 0 01-1-1V4zm3 1h6v4H7V5zm8 8V9H5v4h10z" clip-rule="evenodd" />
                        </svg>
                        Clinic Information
                    </h3>
                    <form action="{{ route('admin.clinic.updateDetails', $clinic->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Clinic Name</label>
                                <input type="text" name="clinic_name" value="{{ old('clinic_name', $clinic->clinic_name) }}" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200 focus:border-indigo-300" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                                <input type="text" name="contact_number" value="{{ old('contact_number', $clinic->contact_number) }}" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200 focus:border-indigo-300" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="address" value="{{ old('address', $clinic->address) }}" 
                                class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200 focus:border-indigo-300" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                            <div class="flex items-center">
                                @if($clinic->profile_picture)
                                    <div class="mr-4">
                                        <img src="{{ asset('storage/' . $clinic->profile_picture) }}" alt="Clinic profile" class="w-16 h-16 rounded-lg object-cover border border-gray-300">
                                    </div>
                                @endif
                                <input type="file" name="profile_picture" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200 focus:border-indigo-300">
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Update Clinic Details
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Update Account Info --}}
                @if ($clinic->user)
                <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Clinic Staff Account
                    </h3>
                    
                    <form action="{{ route('admin.clinic.updateAccount', $clinic->user->id) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $clinic->user->first_name) }}" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-purple-200 focus:border-purple-300" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name', $clinic->user->middle_name) }}" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-purple-200 focus:border-purple-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $clinic->user->last_name) }}" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-purple-200 focus:border-purple-300" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $clinic->user->email) }}" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-purple-200 focus:border-purple-300" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" name="phone_number" value="{{ old('phone_number', $clinic->user->phone_number) }}" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-purple-200 focus:border-purple-300">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                <select name="gender" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-purple-200 focus:border-purple-300">
                                    <option value="">Select Gender</option>
                                    <option value="female" {{ old('gender', $clinic->user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="male" {{ old('gender', $clinic->user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="prefer_not_say" {{ old('gender', $clinic->user->gender) == 'prefer_not_say' ? 'selected' : '' }}>Prefer not to say</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Birthday</label>
                                <input type="date" name="birthday" value="{{ old('birthday', $clinic->user->birthday) }}" 
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-purple-200 focus:border-purple-300">
                            </div>
                        </div>

                        <div class="border-t border-gray-200 mt-6 pt-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Change Password (optional)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                    <input type="password" name="password" 
                                        class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-purple-200 focus:border-purple-300">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                    <input type="password" name="password_confirmation" 
                                        class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:ring focus:ring-purple-200 focus:border-purple-300">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Update Account Info
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
