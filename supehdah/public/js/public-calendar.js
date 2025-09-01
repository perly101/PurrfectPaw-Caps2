document.addEventListener('DOMContentLoaded', function() {
    // Set timezone for Philippines (GMT+8)
    const PHTimeZoneOffset = 8 * 60; // Philippines is UTC+8 (8 hours * 60 minutes)
    
    // Initialize calendar variables with Philippines time
    let currentDate = new Date();
    // Adjust for Philippines timezone
    currentDate = new Date(currentDate.getTime() + (PHTimeZoneOffset * 60000));
    
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let availableDates = [];
    let selectedDate = null;
    let selectedClinicId = document.getElementById('clinic_id')?.value || null;
    
    // Get DOM elements
    const calendarGrid = document.getElementById('calendarGrid');
    const currentMonthElement = document.getElementById('currentMonth');
    const prevMonthButton = document.getElementById('prevMonth');
    const nextMonthButton = document.getElementById('nextMonth');
    const timeSlotsList = document.getElementById('timeSlotsList');
    const dateDisplay = document.getElementById('selectedDate');
    const noSlotsMessage = document.getElementById('noSlotsMessage');
    const slotSelectionArea = document.getElementById('slotSelectionArea');
    
    // Add event listeners
    prevMonthButton.addEventListener('click', () => navigateMonth(-1));
    nextMonthButton.addEventListener('click', () => navigateMonth(1));
    
    // Initial data load
    loadAvailabilityData();
    
    /**
     * Load availability data for the current clinic
     */
    function loadAvailabilityData() {
        if (!selectedClinicId) return;
        
        // Get dates with availability for the clinic
        fetch(`/api/clinic/${selectedClinicId}/availability/dates`)
            .then(response => response.json())
            .then(data => {
                availableDates = data.dates || [];
                renderCalendar();
            })
            .catch(error => {
                console.error('Error loading availability data:', error);
            });
    }
    
    /**
     * Navigate to previous or next month
     */
    function navigateMonth(direction) {
        currentMonth += direction;
        
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear += 1;
        } else if (currentMonth < 0) {
            currentMonth = 11;
            currentYear -= 1;
        }
        
        renderCalendar();
    }
    
    /**
     * Render the calendar for the current month
     */
    function renderCalendar() {
        // Set the month and year display
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        currentMonthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        
        // Clear the calendar grid
        calendarGrid.innerHTML = '';
        
        // Calculate the first day of the month
        const firstDay = new Date(currentYear, currentMonth, 1);
        const startingDay = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.
        
        // Calculate the number of days in the month
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const totalDays = lastDay.getDate();
        
        // Add empty cells for days before the first day of the month
        for (let i = 0; i < startingDay; i++) {
            const cell = document.createElement('div');
            cell.className = 'bg-white p-2 h-24';
            calendarGrid.appendChild(cell);
        }
        
        // Add cells for each day of the month
        const today = new Date();
        
        for (let day = 1; day <= totalDays; day++) {
            const cell = document.createElement('div');
            cell.className = 'bg-white p-2 h-24 relative';
            
            const dateText = document.createElement('div');
            dateText.className = 'absolute top-1 right-2 font-semibold';
            dateText.textContent = day;
            
            // Check if this is today
            if (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
                dateText.className += ' text-blue-600';
            }
            
            // Check if this date has availability
            const dateString = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dateObject = new Date(currentYear, currentMonth, day);
            
            if (dateObject < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
                // Past date
                cell.classList.add('bg-gray-50');
            } else if (availableDates.includes(dateString)) {
                // Date with availability
                cell.classList.add('cursor-pointer', 'hover:bg-green-50', 'availability-date');
                
                // Add availability indicator
                const indicator = document.createElement('div');
                indicator.className = 'h-3 w-3 bg-green-500 rounded-full absolute top-1 left-1';
                cell.appendChild(indicator);
                
                cell.setAttribute('data-date', dateString);
                
                // Add click event listener
                cell.addEventListener('click', () => selectDate(dateString));
            } else {
                // Date with no availability or closed
                cell.classList.add('bg-gray-50');
            }
            
            cell.appendChild(dateText);
            calendarGrid.appendChild(cell);
        }
    }
    
    /**
     * Format a date string to Philippines timezone
     */
    function formatDatePhilippines(dateString) {
        // Create date object
        const date = new Date(dateString);
        
        // Format with Philippines timezone (UTC+8)
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            timeZone: 'Asia/Manila'
        };
        
        return date.toLocaleDateString('en-US', options);
    }
    
    /**
     * Select a date and show available time slots
     */
    function selectDate(dateString) {
        selectedDate = dateString;
        
        if (dateDisplay) {
            const formatDate = formatDatePhilippines(dateString);
            dateDisplay.textContent = formatDate;
        }
        
        // Clear previously selected dates
        document.querySelectorAll('.availability-date').forEach(el => {
            el.classList.remove('bg-green-100', 'border', 'border-green-500');
        });
        
        // Highlight selected date
        const selectedElement = document.querySelector(`[data-date="${dateString}"]`);
        if (selectedElement) {
            selectedElement.classList.add('bg-green-100', 'border', 'border-green-500');
        }
        
        // Show the slot selection area
        if (slotSelectionArea) {
            slotSelectionArea.classList.remove('hidden');
        }
        
        // Load time slots for this date
        loadTimeSlots(dateString);
    }
    
    /**
     * Load available time slots for the selected date
     */
    function loadTimeSlots(dateString) {
        if (!selectedClinicId || !timeSlotsList) return;
        
        // Show loading state
        timeSlotsList.innerHTML = '<div class="text-center py-4">Loading available slots...</div>';
        
        // Get booked slots for this date
        let bookedSlots = [];
        
        // First fetch booked slots
        fetch(`/api/clinics/${selectedClinicId}/appointments/booked-slots/${dateString}`)
            .then(response => response.json())
            .catch(error => {
                console.error('Error fetching booked slots:', error);
                return { bookedSlots: [] };
            })
            .then(bookedData => {
                bookedSlots = bookedData.bookedSlots || [];
                
                // Now fetch available slots
                return fetch(`/api/clinic/${selectedClinicId}/availability/slots/${dateString}`);
            })
            .then(response => response.json())
            .then(data => {
                if (data.data && data.data.slots && data.data.slots.length > 0) {
                    // Mark slots as booked if they appear in bookedSlots
                    const slotsWithBookingStatus = data.data.slots.map(slot => {
                        // Check if this slot is booked
                        const isBooked = bookedSlots.some(bookedSlot => 
                            bookedSlot.start_time === slot.start || 
                            (bookedSlot.start && bookedSlot.start === slot.start)
                        );
                        
                        return {
                            ...slot,
                            isBooked: isBooked,
                            status: isBooked ? 'booked' : 'available'
                        };
                    });
                    
                    renderTimeSlots(slotsWithBookingStatus);
                    
                    // Show summary info
                    const availableCount = slotsWithBookingStatus.filter(s => !s.isBooked).length;
                    const totalCount = slotsWithBookingStatus.length;
                    const summaryElement = document.createElement('div');
                    summaryElement.className = 'p-3 mb-4 bg-blue-50 border border-blue-200 rounded-md';
                    summaryElement.innerHTML = `
                        <p class="text-blue-800 text-sm font-medium">
                            ${availableCount} of ${totalCount} time slots available
                        </p>
                    `;
                    
                    timeSlotsList.insertBefore(summaryElement, timeSlotsList.firstChild);
                    
                    if (noSlotsMessage) {
                        noSlotsMessage.classList.add('hidden');
                    }
                } else {
                    timeSlotsList.innerHTML = '';
                    
                    if (noSlotsMessage) {
                        noSlotsMessage.classList.remove('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading time slots:', error);
                timeSlotsList.innerHTML = '<div class="text-center py-4 text-red-500">Error loading time slots. Please try again.</div>';
            });
    }
    
    /**
     * Format a time string to Philippines timezone display
     */
    function formatTimePhilippines(timeString) {
        // If the timeString is in HH:MM format, convert to a proper datetime
        if (timeString.length <= 5) {
            // We need to attach a date to properly format the time
            const [hours, minutes] = timeString.split(':');
            const date = new Date();
            date.setHours(parseInt(hours, 10), parseInt(minutes, 10), 0);
            
            // Format with Philippines timezone
            const options = {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true,
                timeZone: 'Asia/Manila'
            };
            
            return date.toLocaleTimeString('en-US', options);
        }
        
        // If it's already a full datetime string
        const date = new Date(timeString);
        const options = {
            hour: 'numeric',
            minute: 'numeric',
            hour12: true,
            timeZone: 'Asia/Manila'
        };
        
        return date.toLocaleTimeString('en-US', options);
    }
    
    /**
     * Render time slots in the UI
     */
    function renderTimeSlots(slots) {
        timeSlotsList.innerHTML = '';
        
        slots.forEach(slot => {
            const isBooked = slot.isBooked === true || slot.status === 'booked';
            
            const slotElement = document.createElement('div');
            slotElement.className = `border rounded-md p-3 mb-2 time-slot ${
                isBooked 
                ? 'bg-red-50 border-red-300' 
                : 'bg-white border-gray-200 hover:bg-gray-50 cursor-pointer'
            }`;
            
            slotElement.setAttribute('data-start', slot.start);
            slotElement.setAttribute('data-end', slot.end);
            
            slotElement.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="${isBooked ? 'text-red-600' : 'text-gray-700'} mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="${isBooked 
                                        ? 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' 
                                        : 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'}" />
                            </svg>
                        </span>
                        <span class="text-lg font-medium ${isBooked ? 'text-red-800' : ''}">${slot.display_time}</span>
                        ${isBooked ? '<span class="ml-2 text-sm font-semibold bg-red-100 text-red-800 px-2 py-1 rounded-full">Booked</span>' : ''}
                    </div>
                    <div>
                        ${!isBooked ? `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        ` : ''}
                    </div>
                </div>
            `;
            
            // Only add click event listener if slot isn't booked
            if (!isBooked) {
                slotElement.addEventListener('click', () => selectTimeSlot(slot));
            }
            
            timeSlotsList.appendChild(slotElement);
        });
    }
    
    /**
     * Select a time slot and proceed with appointment
     */
    function selectTimeSlot(slot) {
        // Clear previously selected slots
        document.querySelectorAll('.time-slot').forEach(el => {
            el.classList.remove('bg-blue-50', 'border-blue-500');
        });
        
        // Highlight selected slot
        const selectedElement = document.querySelector(`[data-start="${slot.start}"][data-end="${slot.end}"]`);
        if (selectedElement) {
            selectedElement.classList.add('bg-blue-50', 'border-blue-500');
        }
        
        // Set form values if the appointment form exists
        const dateInput = document.getElementById('appointment_date');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        if (dateInput && startTimeInput && endTimeInput) {
            dateInput.value = selectedDate;
            startTimeInput.value = slot.start;
            endTimeInput.value = slot.end;
            
            // Show the form
            const appointmentForm = document.getElementById('appointmentForm');
            if (appointmentForm) {
                appointmentForm.classList.remove('hidden');
                appointmentForm.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }
});
