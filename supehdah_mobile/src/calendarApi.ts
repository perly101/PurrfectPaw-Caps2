import { API, tryMultipleBaseUrls } from './api';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Define types for calendar availability
export type TimeSlot = {
  start: string;
  end: string;
  display_time: string;
  isBooked?: boolean;
  status?: 'available' | 'booked';
};

export type BookedSlot = {
  start?: string;
  end?: string;
  start_time?: string;
  end_time?: string;
  appointment_id?: number;
  owner_name?: string;
  status?: string;
};

export type AvailabilitySlotsResponse = {
  slots: TimeSlot[];
  totalSlots?: number;
  availableSlots?: number;
  bookedSlots?: number;
};

// Calendar availability API functions
export const getAvailabilityCalendarDates = async (clinicId: number) => {
  try {
    // First try with our new helper that tries multiple base URLs
    try {
      console.log(`Trying to fetch dates with multiple base URLs`);
      const endpoint = `/clinics/${clinicId}/availability/dates`;
      const response = await tryMultipleBaseUrls(endpoint);
      
      if (response && response.data) {
        const dates = response.data.dates || response.data.data?.dates || [];
        const closedDates = response.data.closed_dates || response.data.data?.closed_dates || [];
        if (dates.length > 0 || closedDates.length > 0) {
          console.log(`Success with multiple base URLs method: found ${dates.length} available dates and ${closedDates.length} closed dates`);
          // Log some sample closed dates for debugging
          if (closedDates.length > 0) {
            console.log('Sample closed dates:', closedDates.slice(0, 3));
          }
          return { dates, closed_dates: closedDates };
        }
      }
    } catch (e) {
      console.log('Failed with multiple base URLs method, trying fallback method');
    }
    
    // Fall back to the original method if needed
    const endpoints = [
      `/clinic/${clinicId}/availability/dates`,
      `/api/clinic/${clinicId}/availability/dates`,
      `/clinics/${clinicId}/availability/dates`,
      `/api/clinics/${clinicId}/availability/dates`
    ];
    
    for (const endpoint of endpoints) {
      try {
        console.log(`Trying to fetch dates from endpoint: ${endpoint}`);
        const response = await API.get(endpoint);
        if (response.data && (response.data.dates || response.data.data?.dates)) {
          console.log(`Success with endpoint: ${endpoint}`);
          // Handle different response formats
          const dates = response.data.dates || response.data.data?.dates || [];
          const closedDates = response.data.closed_dates || response.data.data?.closed_dates || [];
          return { dates, closed_dates: closedDates };
        }
      } catch (err) {
        console.log(`Failed with endpoint: ${endpoint}`);
      }
    }
    
    // If all endpoints fail, return mock data
    const mockDates = [];
    const mockClosedDates = [];
    const today = new Date();
    
    // Generate mock available dates for the next 30 days (excluding weekends)
    for (let i = 1; i <= 30; i++) {
      const date = new Date(today);
      date.setDate(today.getDate() + i);
      
      const dateString = date.toISOString().split('T')[0]; // Format as YYYY-MM-DD
      
      // Weekend days are closed
      if (date.getDay() === 0 || date.getDay() === 6) {
        mockClosedDates.push(dateString);
      } else {
        mockDates.push(dateString);
      }
    }
    
    return { dates: mockDates, closed_dates: mockClosedDates };
  } catch (error) {
    console.error('Error fetching calendar availability dates:', error);
    
    // Return mock data as fallback
    const mockDates = [];
    const mockClosedDates = [];
    const today = new Date();
    
    // Generate mock available dates for the next 30 days (marking weekends as closed)
    for (let i = 1; i <= 30; i++) {
      const date = new Date(today);
      date.setDate(today.getDate() + i);
      
      const dateString = date.toISOString().split('T')[0]; // Format as YYYY-MM-DD
      
      // Weekend days are closed
      if (date.getDay() === 0 || date.getDay() === 6) {
        mockClosedDates.push(dateString);
      } else {
        mockDates.push(dateString);
      }
    }
    
    return { dates: mockDates, closed_dates: mockClosedDates };
  }
};

// Helper function to clean phantom appointments
export const cleanPhantomAppointments = async (clinicId: number) => {
  try {
    console.log('Attempting to clean phantom appointments...');
    
    // First try with our new helper that tries multiple base URLs
    try {
      console.log(`Trying to clean phantom appointments with multiple base URLs`);
      const endpoint = `/clinics/${clinicId}/appointments/debug-clean`;
      const response = await tryMultipleBaseUrls(endpoint);
      
      if (response && response.data) {
        console.log('Phantom appointment cleaning results:', response.data);
        return response.data;
      }
    } catch (e) {
      console.log('Failed with multiple base URLs method, trying fallback method');
    }
    
    // Fall back to the original method
    const endpoints = [
      `/clinics/${clinicId}/appointments/debug-clean`,
      `/api/clinics/${clinicId}/appointments/debug-clean`
    ];
    
    for (const endpoint of endpoints) {
      try {
        const response = await API.get(endpoint);
        if (response.data) {
          console.log('Phantom appointment cleaning results:', response.data);
          return response.data;
        }
      } catch (err) {
        console.log(`Failed to clean appointments with endpoint: ${endpoint}`);
      }
    }
    return { success: false, message: 'Failed to clean phantom appointments' };
  } catch (error) {
    console.error('Error cleaning phantom appointments:', error);
    return { success: false, error };
  }
};

export const getAvailabilityCalendarSlots = async (clinicId: number, date: string) => {
  console.log(`getAvailabilityCalendarSlots for clinic ${clinicId} on date ${date}`);
  
  // Try to clean phantom appointments first
  try {
    await cleanPhantomAppointments(clinicId);
  } catch (cleanError) {
    console.log('Failed to clean phantom appointments, continuing anyway');
  }
  
  // Always force a clean API cache by adding a timestamp
  const timestamp = new Date().getTime();
  
  // Define fallback endpoints to try with cache busting
  const endpoints = [
    `/clinics/${clinicId}/availability/slots/${date}?t=${timestamp}`,
    `/clinic/${clinicId}/availability/slots/${date}?t=${timestamp}`,
    `/api/clinics/${clinicId}/availability/slots/${date}?t=${timestamp}`,
    `/api/clinic/${clinicId}/availability/slots/${date}?t=${timestamp}`
  ];

  try {
    // First try to get booked slots for this day to mark them as unavailable
    let bookedSlots: BookedSlot[] = [];
    
    // Try with our new helper that tries multiple base URLs
    try {
      console.log(`Trying to get booked slots with multiple base URLs`);
      
      // Try multiple endpoints for booked slots with different formats (with cache busting)
      const endpoints = [
        `/clinics/${clinicId}/appointments/booked-slots/${date}?t=${timestamp}`,
        `/clinic/${clinicId}/appointments/booked-slots/${date}?t=${timestamp}`,
        `/api/clinics/${clinicId}/appointments/booked-slots/${date}?t=${timestamp}`,
        `/api/clinic/${clinicId}/appointments/booked-slots/${date}?t=${timestamp}`
      ];
      
      // Try standard endpoint first
      let response = await tryMultipleBaseUrls(endpoints[0]);
      
      // If no bookedSlots found, try direct API lookup for appointments on this date
      if (!response?.data?.bookedSlots || response.data.bookedSlots.length === 0) {
        console.log("No booked slots found with standard endpoint, trying appointments lookup");
        
        // Try to get all appointments for this date directly
        try {
          const apptResponse = await tryMultipleBaseUrls(`/clinics/${clinicId}/appointments?date=${date}&t=${timestamp}`);
          if (apptResponse?.data?.appointments && apptResponse.data.appointments.length > 0) {
            console.log(`Found ${apptResponse.data.appointments.length} appointments directly`);
            
            // Convert appointments to bookedSlots format
            bookedSlots = apptResponse.data.appointments.map((appt: any) => ({
              appointment_id: appt.id,
              start_time: appt.appointment_time,
              owner_name: appt.owner_name,
              status: appt.status
            }));
          }
        } catch (apptErr) {
          console.log("Failed to get appointments directly");
        }
      } else if (response?.data?.bookedSlots) {
        bookedSlots = response.data.bookedSlots;
        console.log(`Retrieved ${bookedSlots.length} booked slots with multiple base URLs method`);
      }
      
      // Log the debug data if available
      if (response?.data?.debug && response.data.debug.raw_appointments) {
        console.log("Debug - Raw appointments from database:");
        console.log(JSON.stringify(response.data.debug.raw_appointments, null, 2));
      }
      
      // We'll rely on database for booked slots, but check for any recent bookings
      // that might not have been fully processed yet (stored as references)
      try {
        // Get all keys to check for appointment references
        const keys = await AsyncStorage.getAllKeys();
        const appointmentKeys = keys.filter(k => k.startsWith('appointment_'));
        
        if (appointmentKeys.length > 0) {
          console.log(`Found ${appointmentKeys.length} appointment references, checking for this date`);
          
          // Check each appointment reference
          for (const key of appointmentKeys) {
            try {
              const appointmentStr = await AsyncStorage.getItem(key);
              if (!appointmentStr) continue;
              
              const appointment = JSON.parse(appointmentStr);
              
              // If this appointment is for this clinic and date, add to booked slots
              if (appointment.clinicId === clinicId && appointment.date === date) {
                console.log(`Found recent appointment for this date: ${key}`);
                
                // Add to booked slots if not already there
                const exists = bookedSlots.some((slot: BookedSlot) => 
                  slot.appointment_id === appointment.id || 
                  (slot.start_time === appointment.time)
                );
                
                if (!exists) {
                  bookedSlots.push({
                    appointment_id: appointment.id,
                    start_time: appointment.time,
                    status: 'confirmed'
                  });
                }
              }
            } catch (e) {
              console.log(`Error processing appointment reference ${key}`, e);
            }
          }
        }
      } catch (storageErr) {
        console.log("Failed to check appointment references", storageErr);
      }
      
      // Validate that the booked slots are actual bookings
      if (bookedSlots.length > 0) {
        console.log(`Found ${bookedSlots.length} booked slots for date ${date}:`);
        bookedSlots.forEach((slot: any) => {
          console.log(`- Time: ${slot.start_time}, ID: ${slot.appointment_id}, Name: ${slot.owner_name || 'unknown'}`);
        });
      }
    } catch (e) {
      console.log('Failed with multiple base URLs method for booked slots, continuing with empty booked slots');
    }
    
    // Then get available slots - first try with multiple base URLs method
    try {
      console.log(`Trying to get availability slots with multiple base URLs`);
      const mainEndpoint = `/clinics/${clinicId}/availability/slots/${date}`;
      const response = await tryMultipleBaseUrls(mainEndpoint);
      console.log(`Success with multiple base URLs method for availability slots`);
      
      let slots = [];
      let totalSlots = 0;
      let availableSlots = 0;
      let bookedSlotsCount = 0;
      let responseDailyLimit = 0;
      
      // Handle the new response format
      if (response.data?.slots) {
        slots = response.data.slots;
        totalSlots = response.data.totalSlots || slots.length;
        availableSlots = response.data.availableSlots || slots.filter((s: TimeSlot) => !s.isBooked).length;
        bookedSlots = response.data.bookedSlots || slots.filter((s: TimeSlot) => s.isBooked).length;
        responseDailyLimit = response.data.daily_limit || 0;
      } 
      // Handle the old response format
      else if (response.data?.data?.slots) {
        slots = response.data.data.slots;
        responseDailyLimit = response.data.data.dailyLimit || response.data.data.daily_limit || 0;
      }
      
      if (slots && slots.length > 0) {
        // Process slots to include availability status and information
        const processedSlots = slots.map((slot: TimeSlot) => {
          // Check if this slot is booked - but ignore phantom bookings
          const isBooked = bookedSlots.length > 0 && bookedSlots.some(
            (bookedSlot: BookedSlot) => {
              // Verify this is a valid booking by checking for required fields
              const isValidBooking = bookedSlot && 
                bookedSlot.appointment_id && 
                (bookedSlot.start || bookedSlot.start_time);
                
              // Only consider valid bookings
              if (!isValidBooking) return false;
              
              // Check if this booking matches the current slot
              return bookedSlot.start === slot.start || 
                (bookedSlot.start_time && bookedSlot.start_time === slot.start);
            }
          );
          
          return {
            ...slot,
            isBooked,
            status: isBooked ? 'booked' : 'available'
          };
        });
        
        // Get daily limit from response if available
        const dailyLimit = response.data?.data?.daily_limit || 
                          response.data?.daily_limit || 
                          responseDailyLimit || 20; // Use 20 as fallback
                          
        // Get actual slot count (how many slots were actually generated)
        const actualSlotCount = response.data?.data?.actual_slot_count || processedSlots.length;
        
        console.log(`Daily limit from API response: ${dailyLimit}`);
        console.log(`Actual generated slots: ${actualSlotCount}`);
        
        // Use API provided counts or calculate our own
        const availableSlotCount = availableSlots || processedSlots.filter((s: TimeSlot) => !s.isBooked).length;
        const bookedSlotCount = bookedSlotsCount || processedSlots.filter((s: TimeSlot) => s.isBooked).length;
        const totalSlotCount = totalSlots || processedSlots.length;
        
        // Log detailed information for debugging
        console.log(`Received ${processedSlots.length} time slots`);
        console.log(`Total slots in response: ${totalSlotCount}`);
        console.log(`Available slots in response: ${availableSlotCount}`);
        console.log(`Booked slots in response: ${bookedSlotCount}`);
        console.log(`Unavailable slots (not booked but not available): ${totalSlotCount - availableSlotCount - bookedSlotCount}`);
        
        // Log sample slots for verification
        console.log(`Sample slots:`);
        processedSlots.slice(0, 3).forEach((slot: TimeSlot, idx: number) => {
          console.log(`Slot ${idx + 1}: ${slot.display_time} - ${slot.isBooked ? 'Booked' : 'Available'}`);
        });
        
        // Add total and available counts
        return {
          slots: processedSlots,
          totalSlots: totalSlotCount, // Use the actual number of slots generated
          availableSlots: availableSlotCount,
          bookedSlots: bookedSlotCount
        };
      }
    } catch (multiError) {
      console.log('Failed with multiple base URLs method, trying fallback endpoints');
    }
  } catch (error) {
    console.log('Error in main try-catch block:', error);
    
    // Check if error is axios error with response
    if (error && typeof error === 'object' && 'response' in error) {
      const axiosError = error as any;
      
      // Check for 401 unauthorized error
      if (axiosError.response && axiosError.response.status === 401) {
        console.log('Authentication error (401) detected in calendar API call');
        // The response interceptor in api.ts will handle the redirect to login
        throw error;
      }
    }
    
    console.log('Falling back to mock data after error');
  }
  
  // If we got here, all attempts failed - return mock data
  console.log('All API attempts failed, returning mock data');
  const mockSlots: TimeSlot[] = [
    { start: "09:00:00", end: "09:30:00", display_time: "9:00 AM - 9:30 AM", isBooked: false, status: 'available' },
    { start: "09:30:00", end: "10:00:00", display_time: "9:30 AM - 10:00 AM", isBooked: false, status: 'available' },
    { start: "10:00:00", end: "10:30:00", display_time: "10:00 AM - 10:30 AM", isBooked: false, status: 'available' },
    { start: "10:30:00", end: "11:00:00", display_time: "10:30 AM - 11:00 AM", isBooked: false, status: 'available' },
    { start: "11:00:00", end: "11:30:00", display_time: "11:00 AM - 11:30 AM", isBooked: false, status: 'available' },
    { start: "11:30:00", end: "12:00:00", display_time: "11:30 AM - 12:00 PM", isBooked: false, status: 'available' },
    { start: "13:00:00", end: "13:30:00", display_time: "1:00 PM - 1:30 PM", isBooked: false, status: 'available' },
    { start: "13:30:00", end: "14:00:00", display_time: "1:30 PM - 2:00 PM", isBooked: false, status: 'available' },
    { start: "14:00:00", end: "14:30:00", display_time: "2:00 PM - 2:30 PM", isBooked: false, status: 'available' },
    { start: "14:30:00", end: "15:00:00", display_time: "2:30 PM - 3:00 PM", isBooked: false, status: 'available' },
  ];
  
  // Log the same detailed information for the mock data
  console.log(`Returning mock data with ${mockSlots.length} time slots`);
  console.log(`Total slots: ${mockSlots.length}`);
  console.log(`Available slots: ${mockSlots.filter(s => !s.isBooked).length}`);
  console.log(`Booked slots: ${mockSlots.filter(s => s.isBooked).length}`);
  
  // Log sample slots from mock data
  console.log(`Sample slots from mock data:`);
  mockSlots.slice(0, 3).forEach((slot: TimeSlot, idx: number) => {
    console.log(`Slot ${idx + 1}: ${slot.display_time} - ${slot.isBooked ? 'Booked' : 'Available'}`);
  });
  
  return {
    slots: mockSlots,
    totalSlots: mockSlots.length,
    availableSlots: mockSlots.filter(s => !s.isBooked).length,
    bookedSlots: mockSlots.filter(s => s.isBooked).length
  };
};
