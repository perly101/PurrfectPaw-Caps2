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
                <div class="mb-6">
                    <a href="{{ route('clinic.doctors.index') }}" class="flex items-center text-indigo-600 hover:text-indigo-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Doctors List
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="relative">
            <div class="h-32 bg-gradient-to-r from-indigo-500 to-blue-600"></div>
            <div class="absolute top-16 left-6">
                <div class="h-28 w-28 rounded-full border-4 border-white overflow-hidden bg-white">
                    @if($doctor->photo)
                    <img src="{{ asset('storage/' . $doctor->photo) }}" alt="{{ $doctor->full_name }}" class="h-full w-full object-cover">
                    @else
                    <div class="h-full w-full bg-gray-200 flex items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="pt-20 px-6 pb-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold text-gray-900">Dr. {{ $doctor->full_name }}</h1>
                <div class="flex space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        {{ $doctor->availability_status === 'active' ? 'bg-green-100 text-green-800' : 
                           ($doctor->availability_status === 'on_leave' ? 'bg-yellow-100 text-yellow-800' : 
                           'bg-red-100 text-red-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $doctor->availability_status)) }}
                    </span>
                    <a href="{{ route('clinic.doctors.edit', $doctor->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <div class="col-span-2">
                    <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Professional Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Specialization</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $doctor->specialization }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">License Number</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $doctor->license_number }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Years of Experience</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $doctor->experience_years }} {{ Str::plural('year', $doctor->experience_years) }}</p>
                            </div>
                        </div>
                        
                        @if($doctor->bio)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500">Bio</h3>
                            <div class="mt-1 text-sm text-gray-900">
                                <p>{{ $doctor->bio }}</p>
                            </div>
                        </div>
                        @endif
                        
                        <div class="mt-6 flex">
                            <form action="{{ route('clinic.doctors.update-status', $doctor->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <div class="flex items-center space-x-2">
                                    <label for="status_update" class="text-sm font-medium text-gray-700">Availability Status:</label>
                                    <select id="status_update" name="availability_status" class="focus:ring-indigo-500 focus:border-indigo-500 h-9 py-0 pl-2 pr-7 border-gray-300 rounded-md text-sm">
                                        <option value="active" {{ $doctor->availability_status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="on_leave" {{ $doctor->availability_status === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                        <option value="not_accepting" {{ $doctor->availability_status === 'not_accepting' ? 'selected' : '' }}>Not Accepting Appointments</option>
                                    </select>
                                    <button type="submit" class="bg-white py-1 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Appointments section could be added here in the future -->
                </div>
                
                <div>
                    <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h2>
                        
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm">
                                    <p class="font-medium text-gray-900">Email</p>
                                    <p class="text-gray-700">{{ $doctor->email }}</p>
                                </div>
                            </li>
                            
                            @if($doctor->phone_number)
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm">
                                    <p class="font-medium text-gray-900">Phone</p>
                                    <p class="text-gray-700">{{ $doctor->phone_number }}</p>
                                </div>
                            </li>
                            @endif
                            
                            @if($doctor->gender)
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm">
                                    <p class="font-medium text-gray-900">Gender</p>
                                    <p class="text-gray-700">{{ ucfirst($doctor->gender) }}</p>
                                </div>
                            </li>
                            @endif
                            
                            @if($doctor->birthday)
                            <li class="flex items-start">
                                <div class="flex-shrink-0 h-6 w-6 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm">
                                    <p class="font-medium text-gray-900">Date of Birth</p>
                                    <p class="text-gray-700">{{ \Carbon\Carbon::parse($doctor->birthday)->format('F d, Y') }}</p>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 shadow-sm mt-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Account Information</h2>
                        
                        @if($doctor->user)
                        <div class="text-sm">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Linked Account
                                </span>
                            </div>
                            <p class="text-gray-500">User account is linked to Dr. {{ $doctor->last_name }}</p>
                            <p class="text-gray-700 mt-1">Username: {{ $doctor->user->name }}</p>
                            <p class="text-gray-700">Email: {{ $doctor->user->email }}</p>
                            <p class="text-gray-700">Role: {{ ucfirst($doctor->user->role) }}</p>
                        </div>
                        @else
                        <div class="text-sm">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    No Linked Account
                                </span>
                            </div>
                            <p class="text-gray-500">This doctor doesn't have an associated user account.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
