<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">
            {{ $clinic->name }} - Availability Calendar
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white shadow-xl rounded-lg p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Book an Appointment</h2>
                
                <!-- Timezone indicator -->
                <div class="text-sm bg-blue-50 text-blue-600 px-3 py-1 rounded-full inline-flex items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Showing times in Asia/Manila (Philippines) timezone
                </div>
                
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
                        <span class="text-sm text-gray-600">Available</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-gray-100 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Unavailable</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-100 border border-blue-400 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Selected Date</span>
                    </div>
                </div>
                
                <!-- Selected Date and Time Slots -->
                <div id="slotSelectionArea" class="border-t pt-6 hidden">
                    <h3 id="selectedDate" class="text-xl font-semibold mb-4"></h3>
                    
                    <div id="timeSlotsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <!-- Time slots will be dynamically generated here -->
                    </div>
                    
                    <div id="noSlotsMessage" class="hidden py-4 text-center text-gray-500">
                        No available appointments for this date. Please select another date.
                    </div>
                </div>
                
                <!-- Appointment Form -->
                <div id="appointmentForm" class="hidden mt-8 border-t pt-6">
                    <h3 class="text-xl font-semibold mb-4">Book Your Appointment</h3>
                    
                    <form method="POST" action="{{ route('appointments.store', $clinic->id) }}">
                        @csrf
                        <input type="hidden" name="clinic_id" value="{{ $clinic->id }}">
                        <input type="hidden" name="appointment_date" id="appointment_date">
                        <input type="hidden" name="start_time" id="start_time">
                        <input type="hidden" name="end_time" id="end_time">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                                <input type="text" name="name" id="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" name="email" id="email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" name="phone" id="phone" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                            </div>
                            
                            <div>
                                <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                                <select name="service_id" id="service_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                                    <option value="">Select a service</option>
                                    <!-- Add dynamic service options here -->
                                    <option value="1">General Checkup</option>
                                    <option value="2">Vaccination</option>
                                    <option value="3">Surgery</option>
                                    <option value="4">Dental Cleaning</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="bg-white shadow-xl rounded-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $clinic->name }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Contact Information</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $clinic->address }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ $clinic->phone }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $clinic->email }}</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Regular Hours</h3>
                        <ul class="space-y-1 text-gray-600">
                            <li class="flex justify-between">
                                <span>Monday</span>
                                <span>9:00 AM - 5:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Tuesday</span>
                                <span>9:00 AM - 5:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Wednesday</span>
                                <span>9:00 AM - 5:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Thursday</span>
                                <span>9:00 AM - 5:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Friday</span>
                                <span>9:00 AM - 5:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Saturday</span>
                                <span>Closed</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Sunday</span>
                                <span>Closed</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="clinic_id" value="{{ $clinic->id }}">

    <script src="{{ asset('js/public-calendar.js') }}"></script>
</x-app-layout>
