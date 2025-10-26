// Configurable slot color mapping and booking settings
export interface SlotColorConfig {
  available: string;
  booked: string;
  past: string;
  closed: string;
}

export interface BookingConfig {
  // Restrict bookings to same day only
  samedayOnly: boolean;
  // Message when user tries to select different date
  samedayMessage: string;
  // Color configuration for slot states
  slotColors: SlotColorConfig;
  // Timezone for all operations (Philippines timezone)
  timezone: string;
}

export const SLOT_STATE_MESSAGES = {
  available: 'Available for booking',
  booked: 'Already booked',
  past: 'Time already has passed.',
  closed: 'Currently unavailable'
};

export const BOOKING_CONFIG: BookingConfig = {
  samedayOnly: true,
  samedayMessage: "Bookings are allowed for today only.",
  slotColors: {
    available: '#34D399', // Green - can book
    booked: '#EF4444',    // Red - no longer selectable  
    past: '#9CA3AF',      // Gray - time already passed
    closed: '#F59E0B'     // Yellow/Orange - unavailable/closed
  },
  timezone: 'Asia/Manila'
};