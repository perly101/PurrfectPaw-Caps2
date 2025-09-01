@php
    use App\Models\ClinicInfo;
    $clinic = $clinic ?? ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">
            
            {{-- Sidebar --}}
            <div class="w-1/4">
                @include('clinic.components.sidebar')
            </div>
            
            <div class="flex-1">
                <div class="bg-white shadow-xl rounded-lg p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">Appointment Details</h1>
                        <a href="{{ route('clinic.appointments.index') }}" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-150">
                            Back to List
                        </a>
                    </div>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h2 class="text-lg font-medium text-gray-800 mb-2">Appointment Information</h2>
                                <div class="space-y-2">
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">ID:</span>
                                        <span class="font-medium">#{{ $appointment->id }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Date Created:</span>
                                        <span class="font-medium">{{ $appointment->created_at->format('F d, Y h:i A') }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Current Status:</span>
                                        <span class="font-medium inline-flex px-2 py-1 text-xs rounded-full
                                            {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $appointment->status === 'assigned' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $appointment->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                        ">{{ ucfirst($appointment->status) }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Assigned Doctor:</span>
                                        <span class="font-medium">{{ $appointment->doctor ? $appointment->doctor->getFullNameAttribute() : 'Not assigned' }}</span>
                                    </p>
                                </div>
                                
                                <!-- Scheduled Appointment Box -->
                                <div class="mt-4 bg-blue-50 p-4 rounded-lg border border-blue-100">
                                    <h3 class="text-indigo-700 font-medium mb-2 uppercase text-sm">SCHEDULED APPOINTMENT</h3>
                                    
                                    @php 
                                        // Get raw date/time values directly from attributes
                                        $rawDate = $appointment->getAttributes()['appointment_date'] ?? null;
                                        $rawTime = $appointment->getAttributes()['appointment_time'] ?? null;
                                    @endphp
                                    
                                    <div class="flex items-center mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-gray-800 font-medium">
                                            @if($rawDate)
                                                {{ \Carbon\Carbon::parse($rawDate, 'Asia/Manila')->format('F d, Y') }}
                                            @else
                                                No date set
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-gray-800 font-medium">
                                            @if($rawTime)
                                                {{ \Carbon\Carbon::parse($rawTime, 'Asia/Manila')->format('g:i A') }}
                                            @else
                                                No time set
                                            @endif
                                        </span>
                                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Time Slot Reserved</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h2 class="text-lg font-medium text-gray-800 mb-2">Owner Information</h2>
                                <div class="space-y-2">
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Name:</span>
                                        <span class="font-medium">{{ $appointment->owner_name }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="font-medium">{{ $appointment->owner_phone }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Update Form -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h2 class="text-lg font-medium text-gray-800 mb-2">Update Status</h2>
                        <form action="{{ route('clinic.appointments.update-status', $appointment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="flex items-center space-x-4">
                                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="assigned" {{ $appointment->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="closed" {{ $appointment->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Doctor Assignment Form -->
                    @if(!$appointment->doctor_id)
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h2 class="text-lg font-medium text-gray-800 mb-2">Assign Doctor</h2>
                        <form action="{{ url('/clinic/appointments/' . $appointment->id . '/assign-doctor') }}" method="POST">
                            @csrf
                            <div class="flex items-center space-x-4">
                                <select name="doctor_id" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">-- Select a doctor --</option>
                                    @foreach(App\Models\Doctor::where('clinic_id', $clinic->id)->where('availability_status', 'active')->get() as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->first_name }} {{ $doctor->last_name }} ({{ $doctor->specialization }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition duration-150">
                                    Assign Doctor
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    <!-- Consultation Notes Form -->
                    @if($appointment->doctor_id && $appointment->status === 'completed')
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h2 class="text-lg font-medium text-gray-800 mb-2">Add Consultation Notes</h2>
                        <form action="{{ url('/clinic/appointments/' . $appointment->id . '/add-notes') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <textarea name="consultation_notes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter consultation notes here...">{{ $appointment->consultation_notes }}</textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-150">
                                    Save Notes & Close Case
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    <!-- Display Consultation Notes if Available -->
                    @if($appointment->consultation_notes)
                    <div class="bg-green-50 p-4 rounded-lg mb-6 border border-green-100">
                        <h2 class="text-lg font-medium text-green-800 mb-2">Consultation Notes</h2>
                        <div class="p-3 bg-white rounded shadow-sm">
                            {{ $appointment->consultation_notes }}
                        </div>
                    </div>
                    @endif

                    <!-- Appointment Details -->
                    <div class="bg-white border rounded-lg shadow-sm">
                        <div class="p-4 border-b bg-gray-50">
                            <h2 class="text-lg font-medium text-gray-800">Appointment Details</h2>
                        </div>
                        <div class="p-4">
                            <!-- Appointment Date and Time Summary -->
                            <div class="mb-6 p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                                <h3 class="text-indigo-800 font-semibold mb-2">Selected Appointment Slot</h3>
                                
                                @php 
                                    // Get raw date/time values directly from attributes
                                    $rawDateDetails = $appointment->getAttributes()['appointment_date'] ?? null;
                                    $rawTimeDetails = $appointment->getAttributes()['appointment_time'] ?? null;
                                @endphp
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-700"><span class="font-medium">Date:</span> 
                                            @if($rawDateDetails)
                                                {{ \Carbon\Carbon::parse($rawDateDetails, 'Asia/Manila')->format('l, F d, Y') }}
                                            @else
                                                No date selected
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            (Stored format: {{ $rawDateDetails ?? 'N/A' }})
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-700"><span class="font-medium">Time:</span> 
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-md">
                                                @if($rawTimeDetails)
                                                    {{ \Carbon\Carbon::parse($rawTimeDetails, 'Asia/Manila')->format('g:i A') }}
                                                @else
                                                    No time selected
                                                @endif
                                            </span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            (Stored format: {{ $rawTimeDetails ?? 'N/A' }})
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($appointment->customValues as $value)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $value->field->label }}
                                                @if($value->field->required)
                                                    <span class="text-red-500">*</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-normal text-sm text-gray-500">
                                                @if(is_array($value->value))
                                                    {{ implode(', ', $value->value) }}
                                                @elseif(is_bool($value->value))
                                                    {{ $value->value ? 'Yes' : 'No' }}
                                                @else
                                                    {{ $value->value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
