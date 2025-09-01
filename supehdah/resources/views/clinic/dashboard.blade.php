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

            {{-- Main Dashboard Content --}}
            <div class="w-3/4">
                <div class="bg-white shadow-xl rounded-lg p-8 flex flex-col items-center text-center">

                    @if ($clinic && $clinic->profile_picture)
                        <img src="{{ asset('storage/' . $clinic->profile_picture) }}"
                            class="w-32 h-32 rounded-full object-cover shadow mb-4 border-4 border-indigo-200"
                            alt="Clinic Profile Picture">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mb-4">
                            <span class="text-sm">No Image</span>
                        </div>
                    @endif

                    <h3 class="text-2xl font-semibold text-gray-800 mb-2">
                        Welcome back{{ $clinic ? ', ' . $clinic->clinic_name : '' }}!
                    </h3>

                    <p class="text-gray-600 text-sm">This is your personalized clinic dashboard.</p>

                    <div class="mt-6 bg-gray-50 w-full text-left rounded-md p-4 border">
                        <p class="text-sm text-gray-800"><strong>Address:</strong> {{ $clinic->address ?? 'Not provided' }}</p>
                        <p class="text-sm text-gray-800"><strong>Contact:</strong> {{ $clinic->contact_number ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
