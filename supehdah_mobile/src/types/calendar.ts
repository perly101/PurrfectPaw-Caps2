export const PH_TIMEZONE_OFFSET = 8 * 60 * 60 * 1000; // +8 hours in milliseconds
export const DEFAULT_SLOT_DURATION = 30; // minutes

export type SlotState = 'available' | 'booked' | 'past' | 'closed';

export interface TimeSlot {
    start: string;
    end?: string;
    display_time: string;
    isBooked: boolean;
    status?: 'available' | 'booked' | string;
    duration?: number;
    state: SlotState;
    stateMessage?: string;
    available: boolean;
}


import { BOOKING_CONFIG } from '../config/bookingConfig';

export const slotColors = BOOKING_CONFIG.slotColors;

export const slotStyles = {
    available: {
        backgroundColor: slotColors.available,
        textColor: '#ffffff'
    },
    booked: {
        backgroundColor: slotColors.booked,
        textColor: '#ffffff'
    },
    past: {
        backgroundColor: slotColors.past,
        textColor: '#ffffff'
    },
    closed: {
        backgroundColor: slotColors.closed,
        textColor: '#ffffff'
    }
} as const;
