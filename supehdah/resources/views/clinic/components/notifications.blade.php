{{-- resources/views/clinic/components/notifications.blade.php --}}
@php
    use App\Models\Notification;
    use App\Models\ClinicInfo;
    
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
    
    if ($clinic) {
        $notifications = $clinic->notifications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $unreadCount = $clinic->notifications()
            ->whereNull('read_at')
            ->count();
    } else {
        $notifications = collect();
        $unreadCount = 0;
    }
@endphp

<div class="clinic-notifications">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-indigo-300/90">Recent Notifications</h3>
        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-indigo-500/20 text-xs font-medium text-indigo-300">
            {{ $unreadCount }} unread
        </span>
    </div>

    <div class="space-y-2 max-h-60 overflow-y-auto pr-1 custom-scrollbar">
        @if(count($notifications) > 0)
            @foreach($notifications as $notification)
                <div class="notification-item group flex items-start p-2 rounded-lg {{ !$notification->read_at ? 'bg-indigo-500/10 border-l-2 border-indigo-500' : 'bg-gray-800/40' }} transition-all hover:bg-indigo-600/20">
                    <div class="notification-icon mr-3 mt-0.5">
                        @if($notification->type === 'clinic_new_appointment')
                            <div class="p-1.5 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-300" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @elseif($notification->type === 'clinic_appointment_completed')
                            <div class="p-1.5 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @else
                            <div class="p-1.5 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-300" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="notification-content flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <p class="text-xs font-medium text-white truncate">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <span class="text-xxs text-indigo-400/80 whitespace-nowrap ml-2">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans(['short' => true]) }}
                            </span>
                        </div>
                        <p class="text-xs text-indigo-300/80 line-clamp-2">{{ $notification->data['body'] ?? '' }}</p>
                        
                        <div class="mt-1.5 flex justify-between items-center">
                            <div>
                                @if($notification->type === 'clinic_new_appointment' && isset($notification->data['appointment_id']))
                                    <a href="{{ route('clinic.appointments.show', $notification->data['appointment_id']) }}" 
                                       class="text-xxs text-indigo-400 hover:text-indigo-300 transition-colors">
                                        View Appointment
                                    </a>
                                @endif
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <form action="{{ route('clinic.notifications.mark-read', $notification->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="{{ $notification->read_at ? 'opacity-0' : 'opacity-100' }} text-xxs text-indigo-400 hover:text-indigo-300 transition-all group-hover:opacity-100">
                                        Mark as read
                                    </button>
                                </form>
                                <form action="{{ route('clinic.notifications.destroy', $notification->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="opacity-0 text-xxs text-red-400 hover:text-red-300 transition-all group-hover:opacity-100">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="py-6 flex flex-col items-center justify-center text-center">
                <div class="p-2 bg-indigo-500/10 rounded-full mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-300/60" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                </div>
                <p class="text-xs text-indigo-300/60">No notifications yet</p>
                <p class="text-xxs text-indigo-400/50 mt-1">New notifications will appear here</p>
            </div>
        @endif
    </div>

    <a href="{{ route('clinic.notifications.index') }}" class="mt-3 block text-center text-xs text-indigo-400 hover:text-indigo-300 py-1.5 w-full rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 transition-colors">
        View All Notifications
    </a>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(99, 102, 241, 0.05);
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.3);
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(99, 102, 241, 0.5);
    }
    
    .text-xxs {
        font-size: 0.65rem;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>