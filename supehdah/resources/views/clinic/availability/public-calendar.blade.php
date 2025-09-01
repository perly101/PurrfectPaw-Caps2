@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white shadow-xl rounded-lg p-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ $clinic->name }} - Availability Calendar</h2>
            
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <button id="prevMonth" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg> 
                            Previous
                        </button>
                    </div>
                    <h3 id="currentMonth" class="text-xl font-semibold"></h3>
                    <div>
                        <button id="nextMonth" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                            Next 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow">
                    <div class="grid grid-cols-7 gap-px bg-gray-200">
                        <div class="bg-gray-100 p-2 text-center font-medium">Sun</div>
                        <div class="bg-gray-100 p-2 text-center font-medium">Mon</div>
                        <div class="bg-gray-100 p-2 text-center font-medium">Tue</div>
                        <div class="bg-gray-100 p-2 text-center font-medium">Wed</div>
                        <div class="bg-gray-100 p-2 text-center font-medium">Thu</div>
                        <div class="bg-gray-100 p-2 text-center font-medium">Fri</div>
                        <div class="bg-gray-100 p-2 text-center font-medium">Sat</div>
                    </div>
                    
                    <div id="calendarGrid" class="grid grid-cols-7 gap-px bg-gray-200">
                        <!-- Calendar days will be dynamically generated here -->
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center space-x-4 mb-6">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-100 border border-green-400 rounded-full mr-2"></div>
                    <span class="text-sm">Open</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-100 border border-red-400 rounded-full mr-2"></div>
                    <span class="text-sm">Closed</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-100 border border-yellow-400 rounded-full mr-2"></div>
                    <span class="text-sm">Special Hours</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-purple-100 border border-purple-400 rounded-full mr-2"></div>
                    <span class="text-sm">Holiday</span>
                </div>
            </div>
            
            <!-- Selected Day Details -->
            <div id="selectedDayDetails" class="hidden border-t pt-4">
                <h3 class="text-lg font-semibold mb-3">Availability for <span id="selectedDate"></span></h3>
                
                <div id="dayClosedMessage" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p>The clinic is closed on this day.</p>
                </div>
                
                <div id="daySpecialMessage" class="hidden bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                    <p id="specialDescription"></p>
                </div>
                
                <div id="dayRegularDetails" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-gray-600">Hours:</div>
                            <div id="dayHours" class="font-semibold"></div>
                        </div>
                        <div>
                            <div class="text-gray-600">Available Slots:</div>
                            <div id="availableSlots" class="font-semibold"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="#" id="bookAppointmentBtn" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Book Appointment
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow-xl rounded-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Clinic Weekly Schedule</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 mb-6">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Day</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Status</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Hours</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($weeklySchedule as $day => $schedule)
                        <tr>
                            <td class="px-4 py-2 text-sm font-medium">{{ $day }}</td>
                            <td class="px-4 py-2 text-sm">
                                @if($schedule->is_closed)
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Closed</span>
                                @else
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Open</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm">
                                @if(!$schedule->is_closed)
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="bg-white shadow-xl rounded-lg p-8 mt-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Upcoming Special Dates & Holidays</h2>
            @if(count($specialDates) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 mb-6">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Description</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Status</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Hours (if open)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($specialDates as $date)
                        <tr>
                            <td class="px-4 py-2 text-sm">{{ $date->date->format('M d, Y') }}</td>
                            <td class="px-4 py-2 text-sm">{{ $date->description ?? 'Special Date' }}</td>
                            <td class="px-4 py-2 text-sm">
                                @if($date->is_closed)
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Closed</span>
                                @else
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Special Hours</span>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 text-center py-4">No upcoming special dates.</p>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();
        
        // Weekly schedule data
        const weeklySchedule = @json($weeklyScheduleData);
        
        // Special dates data
        const specialDates = @json($specialDatesData);
        
        // Available slots data by date
        const availableSlots = @json($availableSlotsData);
        
        // Initialize calendar
        updateCalendar(currentMonth, currentYear);
        
        // Event listeners for month navigation
        document.getElementById('prevMonth').addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            updateCalendar(currentMonth, currentYear);
        });
        
        document.getElementById('nextMonth').addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            updateCalendar(currentMonth, currentYear);
        });
        
        // Function to update calendar
        function updateCalendar(month, year) {
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startingDay = firstDay.getDay();
            const totalDays = lastDay.getDate();
            
            // Update month and year display
            const monthNames = ["January", "February", "March", "April", "May", "June",
                               "July", "August", "September", "October", "November", "December"];
            document.getElementById('currentMonth').textContent = monthNames[month] + " " + year;
            
            const calendarGrid = document.getElementById('calendarGrid');
            calendarGrid.innerHTML = '';
            
            // Add empty cells for days before start of month
            for (let i = 0; i < startingDay; i++) {
                const cell = document.createElement('div');
                cell.className = 'bg-white p-2 h-24 md:h-32 border-b border-r';
                calendarGrid.appendChild(cell);
            }
            
            // Add cells for days of the month
            for (let day = 1; day <= totalDays; day++) {
                const date = new Date(year, month, day);
                const dateString = formatDate(date);
                const dayOfWeek = date.getDay();
                
                const cell = document.createElement('div');
                cell.className = 'bg-white p-2 h-24 md:h-32 border-b border-r relative';
                
                // Check if it's today
                if (date.toDateString() === new Date().toDateString()) {
                    cell.className += ' ring-2 ring-blue-500';
                }
                
                // Day number
                const dayNumber = document.createElement('div');
                dayNumber.className = 'font-bold text-right';
                dayNumber.textContent = day;
                cell.appendChild(dayNumber);
                
                // Check if it's a special date
                let isSpecialDate = false;
                let specialDate = null;
                
                for (const dateObj of specialDates) {
                    if (dateObj.date === dateString) {
                        isSpecialDate = true;
                        specialDate = dateObj;
                        break;
                    }
                }
                
                // Determine status based on special date or weekly schedule
                let status;
                let statusClass;
                let statusInfo = '';
                
                if (isSpecialDate) {
                    if (specialDate.is_closed) {
                        status = 'Closed';
                        statusClass = 'bg-purple-100 border-purple-400';
                        statusInfo = specialDate.description || 'Holiday';
                    } else {
                        status = 'Special';
                        statusClass = 'bg-yellow-100 border-yellow-400';
                        statusInfo = specialDate.description || 'Special Hours';
                    }
                } else {
                    // Use weekly schedule
                    const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    const dayName = dayNames[dayOfWeek];
                    
                    if (weeklySchedule[dayName] && weeklySchedule[dayName].is_closed) {
                        status = 'Closed';
                        statusClass = 'bg-red-100 border-red-400';
                    } else if (weeklySchedule[dayName]) {
                        status = 'Open';
                        statusClass = 'bg-green-100 border-green-400';
                        
                        // Get available slots for this date
                        const slots = availableSlots[dateString] || weeklySchedule[dayName].daily_limit;
                        statusInfo = slots + ' slots';
                    } else {
                        status = 'Unknown';
                        statusClass = 'bg-gray-100 border-gray-400';
                    }
                }
                
                // Add status indicator
                const statusIndicator = document.createElement('div');
                statusIndicator.className = `absolute top-2 left-2 w-3 h-3 rounded-full ${statusClass}`;
                cell.appendChild(statusIndicator);
                
                // Add status text
                if (statusInfo) {
                    const statusText = document.createElement('div');
                    statusText.className = 'text-xs mt-1';
                    statusText.textContent = statusInfo;
                    cell.appendChild(statusText);
                }
                
                // Make cell clickable to show details
                cell.style.cursor = 'pointer';
                cell.addEventListener('click', function() {
                    showDayDetails(date, isSpecialDate ? specialDate : null);
                });
                
                calendarGrid.appendChild(cell);
            }
            
            // Add empty cells for days after end of month to complete the grid
            const daysAdded = startingDay + totalDays;
            const remainingDays = daysAdded % 7 === 0 ? 0 : 7 - (daysAdded % 7);
            
            for (let i = 0; i < remainingDays; i++) {
                const cell = document.createElement('div');
                cell.className = 'bg-white p-2 h-24 md:h-32 border-b border-r';
                calendarGrid.appendChild(cell);
            }
        }
        
        // Function to show day details
        function showDayDetails(date, specialDate) {
            const dateString = formatDate(date);
            const dayOfWeek = date.getDay();
            const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const dayName = dayNames[dayOfWeek];
            
            // Update selected date text
            document.getElementById('selectedDate').textContent = date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            // Show the details section
            document.getElementById('selectedDayDetails').classList.remove('hidden');
            
            // Hide all message sections initially
            document.getElementById('dayClosedMessage').classList.add('hidden');
            document.getElementById('daySpecialMessage').classList.add('hidden');
            document.getElementById('dayRegularDetails').classList.add('hidden');
            
            // Update details based on special date or weekly schedule
            if (specialDate) {
                if (specialDate.is_closed) {
                    // Show closed message for special date
                    document.getElementById('dayClosedMessage').classList.remove('hidden');
                    if (specialDate.description) {
                        document.getElementById('daySpecialMessage').classList.remove('hidden');
                        document.getElementById('specialDescription').textContent = specialDate.description;
                    }
                } else {
                    // Show special hours
                    document.getElementById('dayRegularDetails').classList.remove('hidden');
                    document.getElementById('dayHours').textContent = 
                        specialDate.start_time + ' - ' + specialDate.end_time;
                    
                    // Get available slots
                    const slots = availableSlots[dateString] || 0;
                    document.getElementById('availableSlots').textContent = slots + ' available';
                    
                    if (specialDate.description) {
                        document.getElementById('daySpecialMessage').classList.remove('hidden');
                        document.getElementById('specialDescription').textContent = specialDate.description;
                    }
                }
            } else if (weeklySchedule[dayName]) {
                if (weeklySchedule[dayName].is_closed) {
                    // Show closed message for regular closed day
                    document.getElementById('dayClosedMessage').classList.remove('hidden');
                } else {
                    // Show regular hours
                    document.getElementById('dayRegularDetails').classList.remove('hidden');
                    document.getElementById('dayHours').textContent = 
                        formatTime(weeklySchedule[dayName].start_time) + ' - ' + 
                        formatTime(weeklySchedule[dayName].end_time);
                    
                    // Get available slots
                    const slots = availableSlots[dateString] || weeklySchedule[dayName].daily_limit;
                    document.getElementById('availableSlots').textContent = slots + ' available';
                    
                    // Set booking link
                    const bookBtn = document.getElementById('bookAppointmentBtn');
                    bookBtn.href = `/appointments/book?date=${dateString}`;
                }
            } else {
                // Unknown schedule
                document.getElementById('dayClosedMessage').classList.remove('hidden');
            }
        }
        
        // Helper function to format date as YYYY-MM-DD
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        // Helper function to format time
        function formatTime(timeString) {
            if (!timeString) return '';
            
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const formattedHour = hour % 12 || 12;
            
            return formattedHour + ':' + minutes + ' ' + ampm;
        }
    });
</script>
@endsection
