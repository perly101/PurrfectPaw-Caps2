<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">
            
            {{-- Sidebar --}}
            <div class="w-1/4">
                @include('clinic.components.sidebar')
            </div>
            
            {{-- Main Content --}}
            <div class="flex-1">

                <div class="bg-white shadow-xl rounded-lg p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">Patient History</h1>
                        <a href="{{ route('clinic.patients.index') }}" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-150">
                            Back to Patients
                        </a>
                    </div>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    {{-- Patient Info --}}
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="flex flex-wrap justify-between">
                            <div class="mb-4 md:mb-0">
                                <h2 class="text-lg font-medium text-gray-800">{{ $patientInfo->owner_name }}</h2>
                                <p class="text-gray-500">{{ $patientInfo->owner_phone }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-500">First Visit: {{ $appointments->count() > 0 ? $appointments->sortBy('created_at')->first()->created_at->format('F d, Y') : 'N/A' }}</p>
                                <p class="text-gray-500">Total Visits: {{ $appointments->count() }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Pet Information --}}
                    <div class="bg-white border rounded-lg shadow-sm mb-6">
                        <div class="p-4 border-b bg-gray-50">
                            <h2 class="text-lg font-medium text-gray-800">Pet Information</h2>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Pet Name</p>
                                    <p class="text-gray-900">{{ $patientInfo->pet_name ?? 'Not specified' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Type</p>
                                    <p class="text-gray-900">{{ $patientInfo->pet_type ?? 'Not specified' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Breed</p>
                                    <p class="text-gray-900">{{ $patientInfo->pet_breed ?? 'Not specified' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Age</p>
                                    <p class="text-gray-900">{{ $patientInfo->pet_age ?? 'Not specified' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Gender</p>
                                    <p class="text-gray-900">{{ $patientInfo->pet_gender ?? 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Appointment History -->
                    <div class="bg-white border rounded-lg shadow-sm mb-6">
                        <div class="p-4 border-b bg-gray-50">
                            <h2 class="text-lg font-medium text-gray-800">Appointment History</h2>
                        </div>
                        <div class="p-4">
                            @if(count($appointments) > 0)
                                <div class="space-y-6">
                                    @foreach($appointments as $index => $appointment)
                                        <div class="border-b pb-6 last:border-b-0 last:pb-0">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <h3 class="font-medium text-gray-900">
                                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}
                                                        @if($appointment->appointment_time)
                                                            at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                                        @endif
                                                    </h3>
                                                    <p class="text-sm text-gray-500">
                                                        Appointment #{{ $appointment->id }}
                                                    </p>
                                                </div>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ 
                                                        $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                        ($appointment->status === 'assigned' ? 'bg-purple-100 text-purple-800' : 
                                                        ($appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 
                                                        ($appointment->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800' : 
                                                        ($appointment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                        ($appointment->status === 'closed' ? 'bg-gray-100 text-gray-800' : 
                                                        ($appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : ''))))))
                                                    }}
                                                ">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </div>
                                            
                                            @if($appointment->doctor)
                                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span>Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}</span>
                                            </div>
                                            @endif
                                            
                                            @if($appointment->notes && $appointment->status === 'closed')
                                                @php
                                                    $notes = json_decode($appointment->notes, true);
                                                @endphp
                                                
                                                @if($notes && !isset($notes['cancellation_reason']))
                                                    <div class="mt-3 bg-gray-50 rounded-lg p-4 text-sm">
                                                        <h4 class="font-medium text-gray-800 mb-2">Consultation Summary</h4>
                                                        
                                                        <div class="space-y-2">
                                                            @if(isset($notes['diagnosis']))
                                                            <div>
                                                                <span class="font-medium">Diagnosis:</span> 
                                                                <span>{{ $notes['diagnosis'] }}</span>
                                                            </div>
                                                            @endif
                                                            
                                                            @if(isset($notes['chief_complaint']))
                                                                <div>
                                                                    <span class="font-medium">Chief Complaint:</span> 
                                                                    <span>{{ Str::limit($notes['chief_complaint'], 100) }}</span>
                                                                </div>
                                                            @endif
                                                            
                                                            @if(isset($notes['plan_recommendations']))
                                                                <div>
                                                                    <span class="font-medium">Recommendations:</span> 
                                                                    <span>{{ Str::limit($notes['plan_recommendations'], 100) }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($notes && isset($notes['cancellation_reason']))
                                                    <div class="mt-3 bg-red-50 rounded-lg p-4 text-sm">
                                                        <h4 class="font-medium text-red-800 mb-2">Cancelled</h4>
                                                        <p class="text-red-600">Reason: {{ $notes['cancellation_reason'] }}</p>
                                                    </div>
                                                @endif
                                            @endif
                                            
                                            @if($appointment->reason)
                                            <div class="mt-3 text-sm">
                                                <span class="font-medium">Reason for Visit:</span> 
                                                <span class="text-gray-700">{{ $appointment->reason }}</span>
                                            </div>
                                            @endif
                                            
                                            {{-- View Button for all appointment types --}}
                                            <div class="mt-2">
                                                <a href="{{ route('clinic.appointments.show', $appointment->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    View Appointment Details
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No appointment history found.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
