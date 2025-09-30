@php
    use App\Models\ClinicInfo;
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<style>
    /* Custom tooltip style */
    .tooltip {
        position: relative;
    }
    
    .tooltip:hover:after {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 10;
        margin-bottom: 0.25rem;
    }
    
    @media (max-width: 640px) {
        .tooltip:hover:after {
            display: none;
        }
    }
</style>

<x-app-layout>
    <div class="py-8 sm:py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-6">
            
            {{-- Sidebar --}}
            <div class="w-1/4">
                @include('clinic.components.sidebar')
            </div>
            
            {{-- Main Content --}}
            <div class="w-full lg:w-3/4">
                <div class="bg-white shadow-xl rounded-lg p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Doctors Management</h1>
                        <a href="{{ route('clinic.doctors.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 sm:px-4 py-2 rounded-lg flex items-center transition-all duration-200 text-sm sm:text-base">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span>Add Doctor</span>
                        </a>
                    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(count($doctors) > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Specialization</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Experience</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($doctors as $doctor)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 sm:px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($doctor->photo)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $doctor->photo) }}" alt="{{ $doctor->full_name }}">
                                @else
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-800 font-medium text-sm">{{ substr($doctor->first_name, 0, 1) }}{{ substr($doctor->last_name, 0, 1) }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="ml-3 sm:ml-4 truncate">
                                <div class="text-sm font-medium text-gray-900">{{ $doctor->full_name }}</div>
                                <div class="text-xs sm:text-sm text-gray-500 truncate">{{ $doctor->email }}</div>
                                <div class="text-xs text-indigo-600 md:hidden mt-1">{{ $doctor->specialization }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                        {{ $doctor->specialization }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-500 hidden sm:table-cell">
                        {{ $doctor->experience_years }} {{ Str::plural('year', $doctor->experience_years) }}
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <select 
                            class="status-select text-sm rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                            {{ $doctor->availability_status === 'active' ? 'text-green-700 bg-green-100 border-green-200' : 
                               ($doctor->availability_status === 'on_leave' ? 'text-yellow-600 bg-yellow-100 border-yellow-200' : 
                               'text-red-700 bg-red-100 border-red-200') }}
                            w-full sm:w-auto text-xs sm:text-sm"
                            data-doctor-id="{{ $doctor->id }}"
                            data-original-status="{{ $doctor->availability_status }}">
                            <option value="active" {{ $doctor->availability_status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="on_leave" {{ $doctor->availability_status === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                            <option value="not_accepting" {{ $doctor->availability_status === 'not_accepting' ? 'selected' : '' }}>Not Accepting</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex flex-wrap gap-2 items-center justify-start">
                            <a href="{{ route('clinic.doctors.show', $doctor->id) }}" class="flex items-center justify-center p-1.5 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-all duration-200 tooltip" title="View Doctor">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span class="sr-only">View</span>
                            </a>
                            <a href="{{ route('clinic.doctors.edit', $doctor->id) }}" class="flex items-center justify-center p-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-all duration-200 tooltip" title="Edit Doctor">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span class="sr-only">Edit</span>
                            </a>
                            <form action="{{ route('clinic.doctors.destroy', $doctor->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this doctor? This will also remove their user account.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center justify-center p-1.5 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-all duration-200 tooltip" title="Delete Doctor">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="sr-only">Delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @else
    <div class="bg-white p-4 sm:p-6 rounded-lg shadow text-center">
        <div class="mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Doctors Found</h3>
        <p class="text-gray-500 mb-4">You haven't added any doctors to your clinic yet.</p>
        <a href="{{ route('clinic.doctors.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span>Add Your First Doctor</span>
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $(document).ready(function() {
        // Handle status change
        $('.status-select').on('change', function() {
            const doctorId = $(this).data('doctor-id');
            const status = $(this).val();
            const selectElement = $(this);
            const originalStatus = selectElement.attr('data-original-status') || status;
            
            // Save original status if not already saved
            if (!selectElement.attr('data-original-status')) {
                selectElement.attr('data-original-status', originalStatus);
            }
            
            // Update the select appearance based on status
            updateSelectAppearance(selectElement, status);
            
            // Add loading indicator
            if (selectElement.next('.status-message').length) {
                selectElement.next('.status-message').text('Updating...').removeClass('text-green-600 text-red-600').addClass('text-gray-600');
            } else {
                selectElement.after('<span class="status-message text-xs text-gray-600 ml-2">Updating...</span>');
            }
            
            // Disable select during update
            selectElement.prop('disabled', true);
            
            // Send AJAX request to update status
            $.ajax({
                url: `/clinic/doctors/${doctorId}/status`,
                type: 'PATCH',
                data: {
                    availability_status: status
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        selectElement.next('.status-message').text('Updated!').removeClass('text-gray-600').addClass('text-green-600');
                        
                        // Update data attribute with new status
                        selectElement.attr('data-original-status', status);
                        
                        // Reset message after a delay
                        setTimeout(function() {
                            selectElement.next('.status-message').fadeOut(function() {
                                $(this).remove();
                            });
                        }, 2000);
                    } else {
                        // Show error message
                        selectElement.next('.status-message').text('Failed to update').removeClass('text-gray-600').addClass('text-red-600');
                        
                        // Reset to original status
                        selectElement.val(originalStatus);
                        updateSelectAppearance(selectElement, originalStatus);
                        
                        setTimeout(function() {
                            selectElement.next('.status-message').fadeOut(function() {
                                $(this).remove();
                            });
                        }, 2000);
                    }
                },
                error: function(xhr) {
                    console.error('Error updating doctor status:', xhr);
                    
                    // Show error message
                    selectElement.next('.status-message').text('Failed to update').removeClass('text-gray-600').addClass('text-red-600');
                    
                    // Reset to original status
                    selectElement.val(originalStatus);
                    updateSelectAppearance(selectElement, originalStatus);
                    
                    setTimeout(function() {
                        selectElement.next('.status-message').fadeOut(function() {
                            $(this).remove();
                        });
                    }, 2000);
                },
                complete: function() {
                    // Re-enable select after update
                    selectElement.prop('disabled', false);
                }
            });
        });
        
        function updateSelectAppearance(selectElement, status) {
            // Remove existing classes
            selectElement.removeClass('text-green-700 bg-green-100 border-green-200 text-yellow-600 bg-yellow-100 border-yellow-200 text-red-700 bg-red-100 border-red-200');
            
            // Add appropriate classes based on status
            if (status === 'active') {
                selectElement.addClass('text-green-700 bg-green-100 border-green-200');
            } else if (status === 'on_leave') {
                selectElement.addClass('text-yellow-600 bg-yellow-100 border-yellow-200');
            } else {
                selectElement.addClass('text-red-700 bg-red-100 border-red-200');
            }
        }
    });
</script>
@endpush
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
