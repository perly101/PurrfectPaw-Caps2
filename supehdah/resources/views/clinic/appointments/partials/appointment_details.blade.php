<div class="bg-white rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 bg-gray-50">
        <div class="flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Appointment #{{ $appointment->id }}
            </h3>
            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium 
                {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                {{ $appointment->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $appointment->status === 'confirmed' ? 'bg-indigo-100 text-indigo-800' : '' }}
                {{ $appointment->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
            </span>
        </div>
    </div>
    
    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
            <!-- Patient Information -->
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Patient Information</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    <div class="bg-gray-50 rounded-md p-4">
                        <p class="font-medium">{{ $appointment->owner_name }}</p>
                        <p>Phone: {{ $appointment->owner_phone }}</p>
                        @if(isset($appointment->owner_email))
                            <p>Email: {{ $appointment->owner_email }}</p>
                        @endif
                        @if(isset($appointment->pet_name))
                            <p class="mt-2 font-medium">Pet: {{ $appointment->pet_name }}</p>
                        @endif
                        @if(isset($appointment->pet_type))
                            <p>Type: {{ $appointment->pet_type }}</p>
                        @endif
                        @if(isset($appointment->pet_breed))
                            <p>Breed: {{ $appointment->pet_breed }}</p>
                        @endif
                    </div>
                </dd>
            </div>
            
            <!-- Appointment Details -->
            <div>
                <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }} at
                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                </dd>
            </div>
            
            {{-- Service information is not available --}}
            
            <div>
                <dt class="text-sm font-medium text-gray-500">Doctor</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($appointment->doctor)
                        Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
                    @else
                        Not assigned
                    @endif
                </dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500">Created On</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $appointment->created_at->format('M d, Y g:i A') }}
                </dd>
            </div>
            
            @if($appointment->status === 'completed' || $appointment->status === 'cancelled')
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ ucfirst($appointment->status) }} On</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $appointment->updated_at->format('M d, Y g:i A') }}
                </dd>
            </div>
            @endif
            
            <!-- Consultation Notes (if available) -->
            @if($appointment->notes)
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Consultation Notes</dt>
                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-md">
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
                        <p class="whitespace-pre-line">{{ $appointment->notes }}</p>
                    @endif
                </dd>
            </div>
            @endif
            
            <!-- Cancellation Reason (if cancelled) -->
            @if($appointment->status === 'cancelled' && $appointment->cancellation_reason)
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Cancellation Reason</dt>
                <dd class="mt-1 text-sm text-gray-900 bg-red-50 p-4 rounded-md">
                    {{ $appointment->cancellation_reason }}
                </dd>
            </div>
            @endif
            
            <!-- Custom Fields (if any) -->
            @if($appointment->customValues && $appointment->customValues->count() > 0)
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 mb-2">Additional Information</dt>
                <dd class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($appointment->customValues as $value)
                    <div class="bg-gray-50 p-3 rounded-md">
                        <p class="text-xs text-gray-500">{{ $value->field->name }}</p>
                        <p class="text-sm text-gray-900">{{ $value->value ?: 'Not provided' }}</p>
                    </div>
                    @endforeach
                </dd>
            </div>
            @endif
        </dl>
    </div>
</div>