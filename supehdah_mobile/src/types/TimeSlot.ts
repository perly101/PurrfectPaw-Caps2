export interface TimeSlot {
  start: string;
  end?: string;
  display_time: string;
  isBooked?: boolean;
  status?: string;
  duration?: number;
  state?: 'available' | 'booked' | 'past' | 'closed';
  stateMessage?: string;
  available?: boolean;
  availability?: boolean;
}