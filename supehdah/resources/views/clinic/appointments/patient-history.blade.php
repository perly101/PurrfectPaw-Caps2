<x-app-layout>
    <div class="py-6 bg-gray-100 min-h-screen">
        <div class="px-4 sm:px-6 lg:px-8 flex">
            
            {{-- Sidebar --}}
            <div class="w-64 flex-shrink-0 mr-6">
                @include('clinic.components.sidebar')
            </div>
            
            {{-- Main Content --}}
            <div class="flex-1">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Patient Appointment History</h2>
                        <p class="text-gray-500 text-sm mt-1">View detailed appointment records for this patient</p>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('clinic.appointments.archived') }}" 
                           class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm flex items-center transition">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Archived Appointments
                        </a>
                    </div>
                </div>
                
                <div class="bg-white shadow-lg border border-gray-200 rounded-lg p-6">
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-300 rounded-lg shadow-sm text-green-700 p-4 mb-6 flex items-center" role="alert">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-300 rounded-lg shadow-sm text-red-700 p-4 mb-6 flex items-center" role="alert">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    {{-- Patient Info --}}
                    <div class="bg-blue-50 p-5 rounded-lg mb-6 border border-blue-100 shadow-sm">
                        <div class="flex flex-wrap justify-between">
                            <div class="mb-4 md:mb-0">
                                <h2 class="text-xl font-medium text-gray-800 mb-1">
                                    <i class="fas fa-user-circle text-blue-500 mr-1"></i>
                                    {{ $patientInfo->owner_name }}
                                </h2>
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-phone text-gray-500 mr-2"></i>
                                    <p class="text-gray-600">{{ $patientInfo->owner_phone }}</p>
                                </div>
                                @if(isset($patientInfo->owner_email))
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-500 mr-2"></i>
                                    <p class="text-gray-600">{{ $patientInfo->owner_email }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="flex items-center justify-end mb-2">
                                    <i class="fas fa-calendar-day text-gray-500 mr-2"></i>
                                    <p class="text-gray-600">First Visit: {{ $appointments->last()->created_at->format('F d, Y') }}</p>
                                </div>
                                <div class="flex items-center justify-end">
                                    <i class="fas fa-history text-gray-500 mr-2"></i>
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
                            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
                                Appointment History
                            </h2>
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
                                            <div class="absolute left-0 top-1.5 w-8 h-8 rounded-full {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-600' : ($appointment->status === 'completed' || $appointment->status === 'closed' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600') }} flex items-center justify-center shadow-sm">
                                                @if($appointment->status === 'cancelled')
                                                    <i class="fas fa-times"></i>
                                                @elseif($appointment->status === 'completed' || $appointment->status === 'closed')
                                                    <i class="fas fa-check"></i>
                                                @else
                                                    <i class="fas fa-calendar"></i>
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
                                                
                                                {{-- Service information is not available --}}
                                                
                                                @if($appointment->doctor)
                                                    <div class="text-sm mb-3">
                                                        <span class="font-medium text-gray-600">Doctor:</span> 
                                                        Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
                                                    </div>
                                                @endif

                                                @if($appointment->notes)
                                                    <div class="mt-4 bg-gray-50 rounded-lg p-4 text-sm border border-gray-200 shadow-sm">
                                                        <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                                                            <i class="fas fa-file-medical-alt text-blue-600 mr-2"></i>
                                                            Consultation Notes
                                                        </h4>
                                                        
                                                        <div class="mt-3">
                                                            @php
                                                                $notesData = json_decode($appointment->notes, true);
                                                            @endphp
                                                            @if(is_array($notesData))
                                                                <div class="space-y-3">
                                                                    @if(isset($notesData['chief_complaint']))
                                                                    <div>
                                                                        <h4 class="font-medium text-gray-700">Chief Complaint:</h4>
                                                                        <p class="ml-4">{{ $notesData['chief_complaint'] }}</p>
                                                                    </div>
                                                                    @endif
                                                                    
                                                                    @if(isset($notesData['history_observations']))
                                                                    <div>
                                                                        <h4 class="font-medium text-gray-700">History & Observations:</h4>
                                                                        <p class="ml-4">{{ $notesData['history_observations'] }}</p>
                                                                    </div>
                                                                    @endif
                                                                    
                                                                    @if(isset($notesData['examination_findings']))
                                                                    <div>
                                                                        <h4 class="font-medium text-gray-700">Examination Findings:</h4>
                                                                        <p class="ml-4">{{ $notesData['examination_findings'] }}</p>
                                                                    </div>
                                                                    @endif
                                                                    
                                                                    @if(isset($notesData['diagnosis']))
                                                                    <div>
                                                                        <h4 class="font-medium text-gray-700">Diagnosis:</h4>
                                                                        <p class="ml-4">{{ $notesData['diagnosis'] }}</p>
                                                                    </div>
                                                                    @endif
                                                                    
                                                                    @if(isset($notesData['plan_recommendations']))
                                                                    <div>
                                                                        <h4 class="font-medium text-gray-700">Plan & Recommendations:</h4>
                                                                        <p class="ml-4">{{ $notesData['plan_recommendations'] }}</p>
                                                                    </div>
                                                                    @endif
                                                                    
                                                                    @if(isset($notesData['completed_at']))
                                                                    <div class="text-xs text-gray-500 mt-4">
                                                                        Completed on: {{ \Carbon\Carbon::parse($notesData['completed_at'])->format('F j, Y \a\t g:i A') }}
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <p class="text-gray-800 whitespace-pre-line">{{ $appointment->notes }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                {{-- View Button for all appointment types --}}
                                                <div class="mt-4">
                                                    <a href="{{ route('clinic.appointments.show', $appointment->id) }}" 
                                                       class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md border border-blue-200 hover:bg-blue-100 inline-flex items-center text-sm transition-colors shadow-sm">
                                                        <i class="fas fa-search mr-1.5"></i>
                                                        View Full Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <i class="fas fa-folder-open text-5xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-600 mb-2">No appointment history</h3>
                                    <p class="text-gray-500 max-w-md mx-auto">This patient hasn't had any appointments yet. Patient records will appear here once appointments are completed or cancelled.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>