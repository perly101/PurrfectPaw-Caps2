@php
    use App\Models\ClinicInfo;
    $clinic = $clinic ?? ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    <script>
        // Setup CSRF token for all AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the UI
            console.log('UI initialized');
        });
    </script>
    
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
                        <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Manage Availability & Scheduling</h2>
                        <p class="text-gray-500 text-sm mt-1">Set your clinic hours and manage special dates</p>
                    </div>
                    
                    <div class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-full inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Current Timezone: Asia/Manila (UTC+8)
                    </div>
                </div>

                <div class="bg-white shadow-lg border border-gray-200 rounded-lg p-6 mb-6">
                    
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
                    
                    {{-- Weekly Schedule Section --}}
                    
                    {{-- Weekly Schedule --}}
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calendar-week mr-2 text-blue-600"></i>
                        Weekly Schedule
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 mb-6">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Day</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Open</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Hours</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Slots</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($dailySchedules as $day => $schedule)
                                    <tr id="day-row-{{ $day }}">
                                        <td class="px-4 py-2 text-sm font-medium">{{ $day }}</td>
                                        <td class="px-4 py-2 text-sm day-status">
                                            @if($schedule->is_closed)
                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Closed</span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Open</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm day-hours">
                                            @if(!$schedule->is_closed)
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm day-slots">
                                            @if(!$schedule->is_closed)
                                                <span class="text-sm">{{ $schedule->daily_limit ?? $settings->daily_limit }} slots • {{ $schedule->slot_duration ?? $settings->slot_duration }} min</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <button type="button" class="text-blue-600 hover:text-blue-900" 
                                                onclick="editDay('{{ $day }}', {{ $schedule->is_closed ? 'true' : 'false' }}, '{{ $schedule->start_time ?? '' }}', '{{ $schedule->end_time ?? '' }}', {{ $schedule->daily_limit ?? 'null' }}, {{ $schedule->slot_duration ?? 'null' }})">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Edit Day Modal (Using simple JS toggle, can be enhanced with Alpine.js) --}}
                    <div id="editDayModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 items-center justify-center">
                        <div class="bg-white rounded-lg p-6 max-w-md w-full">
                            <h4 class="text-lg font-bold mb-4">Edit Schedule for <span id="modalDayName"></span></h4>
                            
                            <form id="dayScheduleForm" action="{{ route('clinic.availability.daily') }}" method="POST">
                                @csrf
                                <input type="hidden" name="day_of_week" id="modalDayInput">
                                
                                <div class="mb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" value="1" name="is_closed" id="modalClosedInput" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" onchange="toggleHoursVisibility()">
                                        <span class="ml-2 text-gray-700">Closed on this day</span>
                                    </label>
                                </div>
                                
                                <div id="hoursContainer">
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                            <input type="time" name="start_time" id="modalStartInput" class="w-full border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                            <input type="time" name="end_time" id="modalEndInput" class="w-full border-gray-300 rounded-md shadow-sm">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Daily Appointment Limit</label>
                                            <input type="number" name="daily_limit" id="modalDailyLimitInput" class="w-full border-gray-300 rounded-md shadow-sm" min="1" placeholder="Default: {{ $settings->daily_limit }}">
                                            <p class="text-gray-500 text-xs mt-1">Leave blank to use default</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Duration</label>
                                            <select name="slot_duration" id="modalSlotDurationInput" class="w-full border-gray-300 rounded-md shadow-sm">
                                                <option value="">Default ({{ $settings->slot_duration }} min)</option>
                                                <option value="15">15 minutes</option>
                                                <option value="30">30 minutes</option>
                                                <option value="45">45 minutes</option>
                                                <option value="60">60 minutes</option>
                                                <option value="90">90 minutes</option>
                                                <option value="120">120 minutes</option>
                                            </select>
                                            <p class="text-gray-500 text-xs mt-1">Leave as default or set custom</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="formStatusMessage" class="mb-4 hidden">
                                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-2 text-sm" role="alert">
                                        <p id="statusMessageText"></p>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
                
                {{-- Break Times --}}
                <div class="bg-white shadow-lg border border-gray-200 rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-coffee mr-2 text-orange-500"></i>
                        Break Times
                    </h3>
                    <p class="text-gray-600 mb-4">Define break periods (lunch, meetings, etc.) when appointments cannot be scheduled.</p>
                    
                    <form action="{{ route('clinic.availability.breaks.store') }}" method="POST" class="mb-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" required placeholder="e.g., Lunch Break" 
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Day (Optional)</label>
                                <select name="day_of_week" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Every day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                <input type="time" name="start_time" required class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                <input type="time" name="end_time" required class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_recurring" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm">
                                <span class="ml-2 text-gray-700">Recurring weekly</span>
                            </label>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-150 flex items-center">
                                <i class="fas fa-plus mr-2"></i> Add Break
                            </button>
                        </div>
                    </form>
                    
                    @if($breaks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Name</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Day</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Time</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($breaks as $break)
                                        <tr>
                                            <td class="px-4 py-2 text-sm">{{ $break->name }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $break->day_of_week ?? 'Every day' }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                {{ \Carbon\Carbon::parse($break->start_time)->format('g:i A') }} - 
                                                {{ \Carbon\Carbon::parse($break->end_time)->format('g:i A') }}
                                            </td>
                                            <td class="px-4 py-2 text-sm">
                                                <form action="{{ route('clinic.availability.breaks.destroy', $break->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this break time?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No break times defined yet.</p>
                    @endif
                </div>
                
                {{-- Special Dates / Holidays --}}
                <div class="bg-white shadow-lg border border-gray-200 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-red-500"></i>
                        Special Dates & Holidays
                    </h3>
                    <p class="text-gray-600 mb-4">Mark specific dates as closed or with special operating hours.</p>
                    
                    <form id="specialDateForm" action="{{ route('clinic.availability.special-dates.store') }}" method="POST" class="mb-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="date" required min="{{ date('Y-m-d') }}" 
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            
                            <div class="md:col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <input type="text" name="description" placeholder="e.g., Holiday, Staff Training" 
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            
                            <div id="specialDateStartTimeContainer">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time (if open)</label>
                                <input type="time" name="start_time" class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            
                            <div id="specialDateEndTimeContainer">
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Time (if open)</label>
                                <input type="time" name="end_time" class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_closed" id="specialDateClosedCheckbox" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm">
                                <span class="ml-2 text-gray-700">Clinic closed on this date</span>
                            </label>
                        </div>
                        
                        <div id="specialDateStatusMessage" class="mt-4 hidden">
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-2 text-sm" role="alert">
                                <p id="specialDateMessageText"></p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Add Special Date
                            </button>
                        </div>
                        
                        <script>
                            // Initialize the special date time fields when the page loads
                            document.addEventListener('DOMContentLoaded', function() {
                                toggleSpecialDateTimeFields();
                            });
                        </script>
                    </form>
                    
                    @if($specialDates->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Date</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Status</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Hours</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Description</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($specialDates as $date)
                                        <tr>
                                            <td class="px-4 py-2 text-sm">{{ $date->date->format('M d, Y') }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                @if($date->is_closed)
                                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Closed</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Special Hours</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm">
                                                @if(!$date->is_closed)
                                                    {{ \Carbon\Carbon::parse($date->start_time)->format('g:i A') }} - 
                                                    {{ \Carbon\Carbon::parse($date->end_time)->format('g:i A') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm">{{ $date->description ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                <form action="{{ route('clinic.availability.special-dates.destroy', $date->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this special date?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No special dates defined yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Philippines timezone (UTC+8)
        const PH_TIMEZONE = 'Asia/Manila';
        
        // Helper function to format date in Philippines timezone
        function formatDatePhilippines(date) {
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                timeZone: PH_TIMEZONE
            };
            
            return new Date(date).toLocaleDateString('en-US', options);
        }
        
        // Helper function to format time in Philippines timezone
        function formatTimePhilippines(timeString) {
            // If timeString is just HH:MM format
            if (timeString.length <= 5) {
                // Attach a date to properly format the time
                const [hours, minutes] = timeString.split(':');
                const date = new Date();
                date.setHours(parseInt(hours, 10), parseInt(minutes, 10), 0);
                
                const options = {
                    hour: 'numeric',
                    minute: 'numeric',
                    hour12: true,
                    timeZone: PH_TIMEZONE
                };
                
                return date.toLocaleTimeString('en-US', options);
            }
            
            // If it's a full datetime string
            const options = {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true,
                timeZone: PH_TIMEZONE
            };
            
            return new Date(timeString).toLocaleTimeString('en-US', options);
        }
        
        // Helper function to show status messages
        function showStatusMessage(message, isError = false) {
            const alerts = document.createElement('div');
            alerts.className = isError 
                ? 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6'
                : 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6';
            alerts.setAttribute('role', 'alert');
            
            const text = document.createElement('p');
            text.textContent = message;
            alerts.appendChild(text);
            
            // Insert at the top of the content
            const content = document.querySelector('.bg-white.shadow-xl.rounded-lg.p-8.mb-6');
            content.insertBefore(alerts, content.firstChild.nextSibling);
            
            // Auto-hide after 3 seconds
            setTimeout(() => {
                alerts.remove();
            }, 3000);
        }
        
        // After form submission, the page will reload with updated data
        
        // Helper function to format time (HH:MM to h:MM AM/PM)
        function formatTime(timeString) {
            if (!timeString) return '';
            
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const formattedHour = hour % 12 || 12;
            
            return formattedHour + ':' + minutes + ' ' + ampm;
        }
        
        // Modal functionality
        function editDay(day, isClosed, startTime, endTime, dailyLimit = null, slotDuration = null) {
            // Get today's date in Philippines timezone
            const today = new Date();
            
            console.log('Editing day:', day, 'isClosed:', isClosed);
            console.log('Time in Philippines:', formatDatePhilippines(today));
            
            document.getElementById('modalDayName').textContent = day;
            document.getElementById('modalDayInput').value = day;
            document.getElementById('modalClosedInput').checked = isClosed;
            
            // Set default times if none are provided (8 AM to 5 PM Philippines time)
            if (!startTime && !isClosed) startTime = '08:00';
            if (!endTime && !isClosed) endTime = '17:00';
            
            // Only set time values if not closed
            if (!isClosed) {
                document.getElementById('modalStartInput').value = startTime || '';
                document.getElementById('modalEndInput').value = endTime || '';
            } else {
                document.getElementById('modalStartInput').value = '';
                document.getElementById('modalEndInput').value = '';
            }
            
            // Set daily limit and slot duration if provided
            if (dailyLimit !== null && dailyLimit !== 'null') {
                document.getElementById('modalDailyLimitInput').value = dailyLimit;
            } else {
                document.getElementById('modalDailyLimitInput').value = '';
            }
            
            if (slotDuration !== null && slotDuration !== 'null') {
                // Find and select the matching option
                const slotSelect = document.getElementById('modalSlotDurationInput');
                for (let i = 0; i < slotSelect.options.length; i++) {
                    if (slotSelect.options[i].value == slotDuration) {
                        slotSelect.selectedIndex = i;
                        break;
                    }
                }
            } else {
                document.getElementById('modalSlotDurationInput').selectedIndex = 0;
            }
            
            // Toggle hours container visibility based on closed status
            toggleHoursVisibility();
            
            // Show modal
            document.getElementById('editDayModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('editDayModal').classList.add('hidden');
        }
        
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Attach event listener to the closed checkbox for daily schedule
            const closedCheckbox = document.getElementById('modalClosedInput');
            if (closedCheckbox) {
                closedCheckbox.addEventListener('change', toggleHoursVisibility);
            }
            
            // Attach event listener to the special date closed checkbox
            const specialDateClosedCheckbox = document.getElementById('specialDateClosedCheckbox');
            if (specialDateClosedCheckbox) {
                specialDateClosedCheckbox.addEventListener('change', toggleSpecialDateTimeFields);
                // Initialize visibility on page load
                toggleSpecialDateTimeFields();
            }
        });
        
        function toggleHoursVisibility() {
            const isClosed = document.getElementById('modalClosedInput').checked;
            const hoursContainer = document.getElementById('hoursContainer');
            const startInput = document.getElementById('modalStartInput');
            const endInput = document.getElementById('modalEndInput');
            const dailyLimitInput = document.getElementById('modalDailyLimitInput');
            const slotDurationInput = document.getElementById('modalSlotDurationInput');
            
            if (isClosed) {
                hoursContainer.classList.add('opacity-50');
                // Disable and clear time inputs when closed
                startInput.required = false;
                endInput.required = false;
                startInput.disabled = true;
                endInput.disabled = true;
                dailyLimitInput.disabled = true;
                slotDurationInput.disabled = true;
                startInput.value = '';
                endInput.value = '';
            } else {
                hoursContainer.classList.remove('opacity-50');
                // Make time inputs required when open
                startInput.required = true;
                endInput.required = true;
                startInput.disabled = false;
                endInput.disabled = false;
                dailyLimitInput.disabled = false;
                slotDurationInput.disabled = false;
            }
        }
        
        function toggleSpecialDateTimeFields() {
            const isClosed = document.getElementById('specialDateClosedCheckbox').checked;
            const startTimeContainer = document.getElementById('specialDateStartTimeContainer');
            const endTimeContainer = document.getElementById('specialDateEndTimeContainer');
            const startTimeInput = startTimeContainer.querySelector('input');
            const endTimeInput = endTimeContainer.querySelector('input');
            
            if (isClosed) {
                startTimeContainer.classList.add('opacity-50');
                endTimeContainer.classList.add('opacity-50');
                // Clear and disable time inputs when closed
                startTimeInput.required = false;
                endTimeInput.required = false;
                startTimeInput.disabled = true;
                endTimeInput.disabled = true;
                startTimeInput.value = '';
                endTimeInput.value = '';
            } else {
                startTimeContainer.classList.remove('opacity-50');
                endTimeContainer.classList.remove('opacity-50');
                // Enable and require time inputs when open
                startTimeInput.required = true;
                endTimeInput.required = true;
                startTimeInput.disabled = false;
                endTimeInput.disabled = false;
            }
        }
        
        // Special dates form preparation
        document.addEventListener('DOMContentLoaded', function() {
            const specialDateForm = document.getElementById('specialDateForm');
            if (specialDateForm) {
                specialDateForm.addEventListener('submit', function() {
                    console.log('Special date form submitted');
                });
            }
        });
    </script>
    
    <!-- Debug script for form submissions -->
    <script src="{{ asset('js/availability-debug.js') }}"></script>
</x-app-layout>
