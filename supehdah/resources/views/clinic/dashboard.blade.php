@php
    use App\Models\ClinicInfo;
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    {{-- Include mobile navigation (only visible on mobile) --}}
    @include('clinic.components.mobile-nav')

    <div class="py-6 md:py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:space-x-6">

            {{-- Sidebar (hidden on mobile) --}}
            <div class="hidden md:block md:w-1/4 lg:w-1/4">
                @include('clinic.components.sidebar')
            </div>

            {{-- Main Dashboard Content --}}
            <div class="w-full md:w-3/4 mt-16 md:mt-0">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Welcome Card -->
                    <div class="lg:col-span-2 bg-white shadow-xl rounded-lg p-4 md:p-8 flex flex-col items-center text-center">
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

                    <!-- Notifications Card -->
                    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 py-4 px-6 flex justify-between items-center">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                </svg>
                                <h3 class="ml-2 text-white font-semibold">Notifications</h3>
                            </div>
                            @php
                                $unreadNotificationsCount = $clinic->notifications()->whereNull('read_at')->count();
                            @endphp
                            @if($unreadNotificationsCount > 0)
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-indigo-100 bg-white/20 rounded-full">
                                    {{ $unreadNotificationsCount }} unread
                                </span>
                            @endif
                        </div>
                        <div class="p-4">
                            @include('clinic.components.notifications')
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
