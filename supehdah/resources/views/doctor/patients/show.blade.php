<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">
            
            {{-- Sidebar --}}
            <div class="w-1/4">
                @include('doctor.components.sidebar')
            </div>
            
            {{-- Main Content --}}
            <div class="flex-1">
                <div class="bg-white shadow-xl rounded-lg p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">Patient Details & History</h1>
                        <a href="{{ route('doctor.patients.index') }}" 
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
                    <div class="bg-blue-50 p-6 rounded-lg mb-6 border border-blue-100">
                        <div class="flex flex-wrap justify-between">
                            <div class="mb-4 md:mb-0">
                                <h2 class="text-xl font-medium text-gray-800 mb-1">{{ $patientInfo->owner_name }}</h2>
                                <div class="flex items-center mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <p class="text-gray-600">{{ $patientInfo->owner_phone }}</p>
                                </div>
                                @if($patientInfo->owner_email)
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <p class="text-gray-600">{{ $patientInfo->owner_email }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="flex items-center justify-end mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-gray-600">First Visit: {{ $appointments->last()->created_at->format('F d, Y') }}</p>
                                </div>
                                <div class="flex items-center justify-end">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-gray-600">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            {{ $appointments->count() }} {{ Str::plural('Visit', $appointments->count()) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Appointment History --}}
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-800">Appointment History</h2>
                            <span class="ml-3 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $appointments->count() }} {{ Str::plural('Record', $appointments->count()) }}
                            </span>
                        </div>
                        
                        <div class="relative">
                            {{-- Timeline connector --}}
                            <div class="absolute left-4 top-5 bottom-5 w-0.5 bg-gray-200"></div>
                            
                            @if($appointments->count() > 0)
                                <div class="space-y-6">
                                    @foreach($appointments as $index => $appointment)
                                        <div class="relative pl-10 pb-6">
                                            {{-- Timeline dot --}}
                                            <div class="absolute left-0 top-1.5 w-8 h-8 rounded-full {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-600' : ($appointment->status === 'completed' || $appointment->status === 'closed' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600') }} flex items-center justify-center">
                                                @if($appointment->status === 'cancelled')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                @elseif($appointment->status === 'completed' || $appointment->status === 'closed')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            
                                            <div class="bg-white border rounded-lg shadow-sm p-4 {{ $appointment->status === 'cancelled' ? 'border-red-200' : ($appointment->status === 'completed' || $appointment->status === 'closed' ? 'border-green-200' : 'border-blue-200') }}">
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
                                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full
                                                        {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $appointment->status === 'assigned' ? 'bg-purple-100 text-purple-800' : '' }}
                                                        {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $appointment->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                                        {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $appointment->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}
                                                        {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                    ">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </div>
                                            
                                                @if($appointment->notes)
                                                    @php
                                                        $notes = json_decode($appointment->notes, true);
                                                    @endphp
                                                    
                                                    @if($notes && !isset($notes['cancellation_reason']) && $appointment->status === 'closed')
                                                        <div class="mt-4 bg-gray-50 rounded-lg p-4 text-sm border border-gray-200">
                                                            <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                                Consultation Summary
                                                            </h4>
                                                            
                                                            <div class="space-y-2 mt-3">
                                                                @if(isset($notes['diagnosis']))
                                                                    <div class="flex">
                                                                        <span class="font-medium text-gray-700 w-32">Diagnosis:</span> 
                                                                        <span class="text-gray-800">{{ $notes['diagnosis'] }}</span>
                                                                    </div>
                                                                @endif
                                                                
                                                                @if(isset($notes['chief_complaint']))
                                                                    <div class="flex">
                                                                        <span class="font-medium text-gray-700 w-32">Chief Complaint:</span> 
                                                                        <span class="text-gray-800">{{ $notes['chief_complaint'] }}</span>
                                                                    </div>
                                                                @endif
                                                                
                                                                @if(isset($notes['plan_recommendations']))
                                                                    <div class="flex">
                                                                        <span class="font-medium text-gray-700 w-32">Recommendations:</span> 
                                                                        <span class="text-gray-800">{{ $notes['plan_recommendations'] }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @elseif($notes && isset($notes['cancellation_reason']))
                                                        <div class="mt-4 bg-red-50 rounded-lg p-4 text-sm border border-red-200">
                                                            <h4 class="font-medium text-red-800 mb-2 flex items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Cancelled Appointment
                                                            </h4>
                                                            <p class="text-red-700">Reason: {{ $notes['cancellation_reason'] }}</p>
                                                        </div>
                                                    @endif
                                                @endif
                                                
                                                {{-- View Button for all appointment types --}}
                                                <div class="mt-4">
                                                    <a href="{{ route('doctor.appointments.show', $appointment->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View Appointment Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-600 mb-1">No appointment history</h3>
                                    <p class="text-gray-500">This patient hasn't had any appointments yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
