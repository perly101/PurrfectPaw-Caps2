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
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Appointment Management</h1>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $appointment->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $appointment->owner_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $appointment->owner_phone }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $appointment->status === 'assigned' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $appointment->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($appointment->appointment_date)
                                                <div class="text-sm text-gray-700">
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                                </div>
                                                <div class="text-xs bg-blue-100 text-blue-800 inline-flex rounded-full px-2 py-1 mt-1">
                                                    {{ $appointment->appointment_time ?? 'No time set' }}
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">
                                                    {{ $appointment->created_at->format('M d, Y') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <a href="{{ route('clinic.appointments.show', $appointment->id) }}" 
                                                   class="text-blue-600 hover:text-blue-900">View</a>
                                                
                                                @if($appointment->status === 'pending')
                                                    <button 
                                                        type="button" 
                                                        onclick="openAssignModal({{ $appointment->id }})"
                                                        class="text-purple-600 hover:text-purple-900">
                                                        Assign Doctor
                                                    </button>
                                                @endif

                                                @if($appointment->doctor_id)
                                                    <span class="text-green-600">
                                                        Assigned: {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
                                                    </span>
                                                @endif
                                                
                                                <form action="{{ route('clinic.appointments.delete', $appointment->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this appointment? This will make the slot available for booking again.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No appointments found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Doctor Modal -->
    <div id="assignDoctorModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 hidden" style="display: none;">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Assign Doctor to Appointment</h3>
                <button onclick="closeAssignModal()" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="assignDoctorForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="doctor_id">
                        Select Doctor
                    </label>
                    <select id="doctor_id" name="doctor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">-- Select a doctor --</option>
                        @foreach(App\Models\Doctor::where('clinic_id', $clinic->id)->where('availability_status', 'active')->get() as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->first_name }} {{ $doctor->last_name }} ({{ $doctor->specialization }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeAssignModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Assign
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAssignModal(appointmentId) {
            document.getElementById('assignDoctorForm').action = "{{ url('/clinic/appointments') }}/" + appointmentId + "/assign-doctor";
            const modal = document.getElementById('assignDoctorModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }

        function closeAssignModal() {
            const modal = document.getElementById('assignDoctorModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    </script>
</x-app-layout>
