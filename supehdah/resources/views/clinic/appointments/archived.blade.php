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
                        <h1 class="text-2xl font-bold text-gray-800">Completed & Cancelled Appointments</h1>
                        <a href="{{ route('clinic.appointments.index') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150">
                            Active Appointments
                        </a>
                    </div>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    <div class="mb-4 text-sm text-gray-600">
                        <p>Click on a patient name to view their complete appointment history.</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <div class="divide-y divide-gray-200 border rounded-lg">
                            @forelse($patients as $patient)
                                <div class="patient-container">
                                    <!-- Patient Header - Clickable -->
                                    <a href="{{ route('clinic.appointments.patient-history', ['name' => $patient->owner_name, 'phone' => $patient->owner_phone]) }}" 
                                       class="bg-gray-50 px-6 py-4 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition duration-150">
                                        <div class="flex items-center space-x-3">
                                            <div class="text-gray-900 font-medium">{{ $patient->owner_name }}</div>
                                            <div class="text-gray-500">{{ $patient->owner_phone }}</div>
                                            <div class="text-xs bg-indigo-100 text-indigo-800 rounded-full px-2 py-1">
                                                {{ $patient->appointments->count() }} appointments
                                            </div>
                                        </div>
                                        <div>
                                            <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="px-6 py-8 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500">No archived appointments found</p>
                                    <p class="mt-1 text-gray-500">Completed and cancelled appointments will appear here.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        {{ $patients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>