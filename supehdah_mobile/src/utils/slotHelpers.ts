import { format, isAfter, isBefore, parseISO, startOfDay } from 'date-fns';
import { TimeSlot, SlotState } from '../types/calendar';

export const updateSlotStates = (slots: TimeSlot[]): TimeSlot[] => {
    const now = new Date();
    const today = startOfDay(now);

    return slots.map(slot => {
        const startTime = parseISO(slot.start);
        
        if (slot.isBooked) {
            return { ...slot, state: 'booked' as SlotState };
        }
        
        if (isBefore(startTime, now)) {
            return { ...slot, state: 'past' as SlotState };
        }

        if (!isSameDay(startTime, today)) {
            return { ...slot, state: 'closed' as SlotState };
        }

        return { ...slot, state: 'available' as SlotState };
    });
};

export const isSameDay = (date1: Date, date2: Date): boolean => {
    return format(date1, 'yyyy-MM-dd') === format(date2, 'yyyy-MM-dd');
};

export const formatTimeSlot = (slot: TimeSlot): string => {
    const startTime = parseISO(slot.start);
    return format(startTime, 'h:mm a');
};

export const isSlotBookable = (slot: TimeSlot): boolean => {
    return slot.state === 'available' && !slot.isBooked;
};

export const getSlotStateMessage = (slot: TimeSlot): string => {
    switch (slot.state) {
        case 'available':
            return 'Available';
        case 'booked':
            return 'Already Booked';
        case 'past':
            return 'Past Time Slot';
        case 'closed':
            return 'Not Available';
        default:
            return '';
    }
};