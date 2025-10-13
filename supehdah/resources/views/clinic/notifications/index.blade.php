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

            {{-- Main Content --}}
            <div class="w-full md:w-3/4 mt-16 md:mt-0">
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="p-4 md:p-6 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">Notifications</h2>
                            <div class="flex space-x-4">
                                <a href="{{ route('clinic.notifications.settings') }}" class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Settings
                                </a>
                                <form action="{{ route('clinic.notifications.mark-all-read') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                        Mark all as read
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y">
                        @forelse ($notifications as $notification)
                            <div class="p-4 md:p-5 flex {{ !$notification->read_at ? 'bg-blue-50' : '' }} hover:bg-gray-50 transition-colors">
                                <div class="mr-4 flex-shrink-0">
                                    @if($notification->type === 'clinic_new_appointment')
                                        <div class="p-2 bg-blue-100 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'clinic_appointment_completed')
                                        <div class="p-2 bg-green-100 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="p-2 bg-indigo-100 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <h3 class="text-sm font-semibold {{ !$notification->read_at ? 'text-blue-700' : 'text-gray-800' }}">
                                            {{ $notification->data['title'] ?? 'Notification' }}
                                        </h3>
                                        <div class="flex space-x-4">
                                            <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">{{ $notification->data['body'] ?? '' }}</p>
                                    <div class="mt-2 flex justify-between">
                                        <div>
                                            @if($notification->type === 'clinic_new_appointment' && isset($notification->data['appointment_id']))
                                                <a href="{{ route('clinic.appointments.show', $notification->data['appointment_id']) }}" 
                                                class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800">
                                                    View Appointment
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                        <div class="flex space-x-3">
                                            @if(!$notification->read_at)
                                                <form action="{{ route('clinic.notifications.mark-read', $notification->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                                        Mark as read
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('clinic.notifications.destroy', $notification->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <div class="inline-flex items-center justify-center h-16 w-16 bg-gray-100 rounded-full mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <p class="text-gray-600">You have no notifications at the moment.</p>
                                <p class="text-sm text-gray-500 mt-1">Notifications will appear here when there's activity related to your clinic.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="p-4 border-t">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>