/**
 * Clinic Availability & Scheduling Script
 * Handles client-side functionality for appointment scheduling
 */

class ClinicAvailability {
    constructor(clinicId) {
        this.clinicId = clinicId;
        this.availableSlots = [];
        this.selectedDate = null;
    }

    /**
     * Initialize the date picker and attach event listeners
     * @param {string} datePickerId - The ID of the date input element
     * @param {string} timeSlotsContainerId - The ID of the container for time slots
     */
    init(datePickerId, timeSlotsContainerId) {
        this.datePickerElement = document.getElementById(datePickerId);
        this.timeSlotsContainer = document.getElementById(timeSlotsContainerId);
        
        if (!this.datePickerElement || !this.timeSlotsContainer) {
            console.error('Required elements not found');
            return;
        }
        
        // Set min date to today
        const today = new Date();
        const todayFormatted = today.toISOString().split('T')[0];
        this.datePickerElement.setAttribute('min', todayFormatted);
        
        // Attach event listener for date change
        this.datePickerElement.addEventListener('change', (e) => {
            this.selectedDate = e.target.value;
            this.fetchAvailableSlots(this.selectedDate);
        });
    }

    /**
     * Fetch available slots for the selected date
     * @param {string} date - The selected date in YYYY-MM-DD format
     */
    fetchAvailableSlots(date) {
        if (!date) return;
        
        this.timeSlotsContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><span class="ml-2">Loading available slots...</span></div>';
        
        fetch(`/api/clinics/${this.clinicId}/availability/slots/${date}`)
            .then(response => response.json())
            .then(data => {
                this.availableSlots = data.data.slots || [];
                this.renderSlots();
            })
            .catch(error => {
                console.error('Error fetching slots:', error);
                this.timeSlotsContainer.innerHTML = '<div class="text-center py-4 text-red-500">Error loading available slots. Please try again.</div>';
            });
    }

    /**
     * Render the available time slots
     */
    renderSlots() {
        this.timeSlotsContainer.innerHTML = '';
        
        if (this.availableSlots.length === 0) {
            this.timeSlotsContainer.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    No available slots for this date. Please select another date.
                </div>
            `;
            return;
        }
        
        const slotContainer = document.createElement('div');
        slotContainer.className = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2';
        
        this.availableSlots.forEach(slot => {
            const slotButton = document.createElement('button');
            slotButton.type = 'button';
            slotButton.className = 'py-2 px-4 bg-gray-100 hover:bg-blue-100 text-gray-800 rounded border border-gray-300 text-center transition-colors';
            slotButton.textContent = slot.display_time;
            slotButton.dataset.start = slot.start;
            slotButton.dataset.end = slot.end;
            
            slotButton.addEventListener('click', () => {
                // Remove selected class from all slots
                document.querySelectorAll('.slot-selected').forEach(el => {
                    el.classList.remove('slot-selected', 'bg-blue-500', 'text-white');
                    el.classList.add('bg-gray-100', 'text-gray-800');
                });
                
                // Add selected class to this slot
                slotButton.classList.remove('bg-gray-100', 'text-gray-800');
                slotButton.classList.add('slot-selected', 'bg-blue-500', 'text-white');
                
                // Update hidden field with selected time
                const timeInput = document.getElementById('appointment_time');
                if (timeInput) {
                    timeInput.value = slot.start;
                }
            });
            
            slotContainer.appendChild(slotButton);
        });
        
        this.timeSlotsContainer.appendChild(slotContainer);
    }
}

// Global instance for use in pages
window.ClinicAvailability = ClinicAvailability;
