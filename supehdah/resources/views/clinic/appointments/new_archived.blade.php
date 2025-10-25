@php
    use App\Models\ClinicInfo;
    $clinic = $clinic ?? ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    <div class="py-6 bg-gray-100 min-h-screen">
        <div class="px-4 sm:px-6 lg:px-8 flex">
            
            {{-- Sidebar --}}
            <div class="w-64 flex-shrink-0 mr-6">
                @include('clinic.components.sidebar')
            </div>
            
            <div class="flex-1">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Completed & Cancelled Appointments</h2>
                        <p class="text-gray-500 text-sm mt-1">View patient appointment history</p>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('clinic.appointments.index') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center transition">
                            <i class="fas fa-calendar-alt mr-2"></i> Active Appointments
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
                    
                    <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200 flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                        <p class="text-sm text-blue-800">
                            Click on a patient name to view their complete appointment history.
                        </p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <div class="divide-y divide-gray-200 border border-gray-200 rounded-lg shadow">
                            @if(isset($patients) && count($patients) > 0)
                                @foreach($patients as $patient)
                                    <div class="patient-container">
                                        <!-- Patient Header - Clickable -->
                                        <a href="{{ route('clinic.appointments.patient-history', ['name' => $patient->owner_name, 'phone' => $patient->owner_phone]) }}" 
                                           class="bg-gray-50 px-6 py-4 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition duration-150">
                                            <div class="flex items-center space-x-3">
                                                <div>
                                                    <i class="fas fa-user-circle text-gray-400 text-xl"></i>
                                                </div>
                                                <div>
                                                    <div class="text-gray-900 font-medium">{{ $patient->owner_name }}</div>
                                                    <div class="text-gray-500 text-sm">{{ $patient->owner_phone }}</div>
                                                </div>
                                                <div class="text-xs bg-indigo-100 text-indigo-800 rounded-full px-2 py-1">
                                                    {{ $patient->appointments_count ?? ($patient->appointments->count() ?? 0) }} appointments
                                                </div>
                                            </div>
                                            <div>
                                                <i class="fas fa-chevron-right text-gray-400"></i>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-6 py-8 text-center">
                                    <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-lg font-medium text-gray-500">No archived appointments found</p>
                                    <p class="mt-1 text-gray-400">Completed and cancelled appointments will appear here.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if(isset($patients) && method_exists($patients, 'links'))
                        <div class="mt-6 bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                            {{ $patients->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
