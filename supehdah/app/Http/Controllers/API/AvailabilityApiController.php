<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClinicAvailabilitySetting;
use App\Models\ClinicBreak;
use App\Models\ClinicDailySchedule;
use App\Models\ClinicInfo;
use App\Models\ClinicSpecialDate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityApiController extends Controller
{
    /**
     * Get availability settings for a clinic
     *
     * @param  int  $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings($clinicId)
    {
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        $settings = ClinicAvailabilitySetting::where('clinic_id', $clinicId)->first();
        if (!$settings) {
            $settings = new ClinicAvailabilitySetting([
                'clinic_id' => $clinicId,
                'daily_limit' => 20,
                'slot_duration' => 30,
                'default_start_time' => '09:00',
                'default_end_time' => '17:00',
            ]);
        }
        
        return response()->json([
            'data' => [
                'daily_limit' => $settings->daily_limit,
                'slot_duration' => $settings->slot_duration,
                'default_start_time' => $settings->default_start_time,
                'default_end_time' => $settings->default_end_time,
            ]
        ]);
    }
    
    /**
     * Get the schedule for a specific day of the week
     *
     * @param  int  $clinicId
     * @param  string  $dayOfWeek
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailySchedule($clinicId, $dayOfWeek)
    {
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        $settings = ClinicAvailabilitySetting::where('clinic_id', $clinicId)->first();
        $defaultStart = $settings ? $settings->default_start_time : '09:00';
        $defaultEnd = $settings ? $settings->default_end_time : '17:00';
        
        $schedule = ClinicDailySchedule::where('clinic_id', $clinicId)
            ->where('day_of_week', $dayOfWeek)
            ->first();
            
        if (!$schedule) {
            $schedule = new ClinicDailySchedule([
                'day_of_week' => $dayOfWeek,
                'start_time' => $defaultStart,
                'end_time' => $defaultEnd,
                'is_closed' => in_array($dayOfWeek, ['Saturday', 'Sunday']),
            ]);
        }
        
        return response()->json([
            'data' => [
                'day_of_week' => $schedule->day_of_week,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'is_closed' => $schedule->is_closed,
            ]
        ]);
    }
    
    /**
     * Get all break times for a clinic
     *
     * @param  int  $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBreaks($clinicId)
    {
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        $breaks = ClinicBreak::where('clinic_id', $clinicId)->get();
        
        return response()->json([
            'data' => $breaks->map(function($break) {
                return [
                    'id' => $break->id,
                    'name' => $break->name,
                    'day_of_week' => $break->day_of_week,
                    'start_time' => $break->start_time,
                    'end_time' => $break->end_time,
                    'is_recurring' => $break->is_recurring,
                ];
            })
        ]);
    }
    
    /**
     * Get all special dates (holidays, etc.) for a clinic
     *
     * @param  int  $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpecialDates($clinicId)
    {
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        $specialDates = ClinicSpecialDate::where('clinic_id', $clinicId)
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->get();
        
        return response()->json([
            'data' => $specialDates->map(function($date) {
                return [
                    'id' => $date->id,
                    'date' => $date->date->format('Y-m-d'),
                    'is_closed' => $date->is_closed,
                    'start_time' => $date->start_time,
                    'end_time' => $date->end_time,
                    'description' => $date->description,
                ];
            })
        ]);
    }
    
    /**
     * Get available time slots for booking on a specific date
     *
     * @param  int  $clinicId
     * @param  string  $date (YYYY-MM-DD)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSlots($clinicId, $date)
    {
        $clinic = ClinicInfo::findOrFail($clinicId);
        $carbon_date = Carbon::parse($date);
        
        // Check if clinic is open today
        if (!$clinic->is_open) {
            return response()->json([
                'data' => [
                    'is_available' => false,
                    'message' => 'The clinic is currently closed.',
                    'slots' => [],
                ]
            ]);
        }
        
        // Check if the clinic is closed on this specific day first
        $dayOfWeek = $carbon_date->format('l'); // Get day name (Monday, Tuesday, etc.)
        $dailySchedule = ClinicDailySchedule::where('clinic_id', $clinic->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();
            
        // Check if it's a special closed date
        $specialDate = ClinicSpecialDate::where('clinic_id', $clinic->id)
            ->where('date', $carbon_date->format('Y-m-d'))
            ->first();
            
        if (($specialDate && $specialDate->is_closed) || ($dailySchedule && $dailySchedule->is_closed)) {
            return response()->json([
                'data' => [
                    'is_available' => false,
                    'message' => 'The clinic is closed on this day.',
                    'slots' => [],
                ]
            ]);
        }

        $slots = $this->calculateAvailableSlots($clinic, $carbon_date);
            
        // Get the correct daily limit (from day-specific setting or from general settings)
        $settings = ClinicAvailabilitySetting::where('clinic_id', $clinic->id)->first();
        $dailyLimit = $settings->daily_limit; // Default from general settings
        
        // If we have day-specific settings and a daily limit is set, use that instead
        if ($dailySchedule && $dailySchedule->daily_limit) {
            $dailyLimit = $dailySchedule->daily_limit;
            // Log to help with debugging
            \Illuminate\Support\Facades\Log::info("Using day-specific daily limit for $dayOfWeek: $dailyLimit");
        } else {
            // Log to help with debugging
            \Illuminate\Support\Facades\Log::info("Using default daily limit: $dailyLimit for $dayOfWeek");
        }
            
        // Get all possible slots (before filtering out booked ones)
        $allPossibleSlots = $this->calculateAllPossibleSlots($clinic, $carbon_date);
        
        // Get appointment count for this date
        // Get only non-cancelled appointments
        $appointments = Appointment::where('clinic_id', $clinic->id)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->get();
        $appointmentCount = $appointments->count();
        
        // Log appointments for debugging
        \Illuminate\Support\Facades\Log::info("Found $appointmentCount valid appointments for date: $date");
        
        // List all booked times for debugging
        if ($appointmentCount > 0) {
            $bookedTimes = $appointments->pluck('appointment_time')->toArray();
            \Illuminate\Support\Facades\Log::info("Booked times:", $bookedTimes);
        }
        
        // Get available slots after filtering out booked ones
        $availableSlots = $this->calculateAvailableSlots($clinic, $carbon_date);
        
        // Ensure that we don't exceed the daily limit
        if (count($availableSlots) + $appointmentCount > $dailyLimit) {
            $availableSlotsToKeep = max(0, $dailyLimit - $appointmentCount);
            $availableSlots = array_slice($availableSlots, 0, $availableSlotsToKeep);
        }
        
        $availableCount = count($availableSlots);
        
        // Calculate total slots before filtering (all possible slots)
        $totalSlotCount = count($allPossibleSlots);
        
        // Log this information for debugging
        \Illuminate\Support\Facades\Log::info("Daily limit: $dailyLimit, Total slots: $totalSlotCount, Available: $availableCount, Booked: $appointmentCount");
        
        // Create a complete slots array that includes both available and booked slots
        $completeSlots = [];
        $bookedTimes = $appointments->pluck('appointment_time')->toArray();
        
        // Get all possible slots
        foreach ($allPossibleSlots as $slot) {
            $isBooked = in_array($slot['start'], $bookedTimes);
            $completeSlots[] = [
                'start' => $slot['start'],
                'end' => $slot['end'], 
                'display_time' => $slot['display_time'],
                'duration' => $slot['duration'],
                'isBooked' => $isBooked,
                'status' => $isBooked ? 'booked' : 'available'
            ];
        }
        
        return response()->json([
            'slots' => $completeSlots, // Now includes both available AND booked slots
            'totalSlots' => $totalSlotCount,
            'availableSlots' => $availableCount,
            'bookedSlots' => $appointmentCount,
            'daily_limit' => $dailyLimit,
            'is_available' => $availableCount > 0,
            'date' => $date,
            'booked_times' => $bookedTimes // Add this for debugging
        ]);
    }
    
    /**
     * Calculate available time slots for a given date
     */
    /**
     * Calculate all possible time slots for a given date before filtering out booked ones
     */
    private function calculateAllPossibleSlots($clinic, $date)
    {
        // Get clinic settings
        $settings = ClinicAvailabilitySetting::where('clinic_id', $clinic->id)->first();
        if (!$settings) {
            return [];
        }
        
        $dayOfWeek = $date->format('l'); // Get day name (Monday, Tuesday, etc.)
        
        // Check if it's a special date
        $specialDate = ClinicSpecialDate::where('clinic_id', $clinic->id)
            ->where('date', $date->format('Y-m-d'))
            ->first();
            
        if ($specialDate && $specialDate->is_closed) {
            return []; // Clinic is closed on this special date
        }
        
        // Get operating hours
        if ($specialDate && $specialDate->start_time) {
            // Use special date hours
            $startTime = Carbon::parse($specialDate->start_time, 'Asia/Manila');
            $endTime = Carbon::parse($specialDate->end_time, 'Asia/Manila');
        } else {
            // Use regular schedule for this day of week
            $dailySchedule = ClinicDailySchedule::where('clinic_id', $clinic->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();
                
            if (!$dailySchedule) {
                // Use default settings
                $startTime = Carbon::parse($settings->default_start_time, 'Asia/Manila');
                $endTime = Carbon::parse($settings->default_end_time, 'Asia/Manila');
            } elseif ($dailySchedule->is_closed) {
                return []; // Clinic is closed on this day
            } else {
                $startTime = Carbon::parse($dailySchedule->start_time, 'Asia/Manila');
                $endTime = Carbon::parse($dailySchedule->end_time, 'Asia/Manila');
            }
        }
        
        // Get day-specific settings
        $dailySchedule = ClinicDailySchedule::where('clinic_id', $clinic->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();
        
        // Get slot duration in minutes - use day-specific if available
        $slotDuration = $settings->slot_duration;
        if ($dailySchedule && $dailySchedule->slot_duration) {
            $slotDuration = $dailySchedule->slot_duration;
        }
        
        // Generate all possible slots strictly within the operating hours
        $slots = [];
        $current = clone $startTime;
        
        while ($current->lt($endTime)) {
            $slotEnd = (clone $current)->addMinutes($slotDuration);
            
            // Ensure the ENTIRE slot (start to end) is within operating hours
            if ($slotEnd->lte($endTime)) {
                $slots[] = [
                    'start' => $current->format('H:i:s'),
                    'end' => $slotEnd->format('H:i:s'),
                    'display_time' => $current->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    'duration' => $slotDuration,
                ];
            } else {
                // Stop if adding this slot would exceed the end time
                break;
            }
            
            $current = $slotEnd;
        }
        
        // Remove slots that overlap with breaks
        $breaks = ClinicBreak::where('clinic_id', $clinic->id)
            ->where(function($query) use ($dayOfWeek) {
                $query->whereNull('day_of_week')
                      ->orWhere('day_of_week', $dayOfWeek);
            })
            ->get();
            
        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($break->start_time, 'Asia/Manila');
            $breakEnd = Carbon::parse($break->end_time, 'Asia/Manila');
            
            // Filter out slots that overlap with this break
            $slots = array_filter($slots, function($slot) use ($breakStart, $breakEnd) {
                $slotStart = Carbon::parse($slot['start'], 'Asia/Manila');
                $slotEnd = Carbon::parse($slot['end'], 'Asia/Manila');
                
                // Check if slot is entirely before break or entirely after break
                return $slotEnd->lte($breakStart) || $slotStart->gte($breakEnd);
            });
        }
        
        // Reset array keys
        return array_values($slots);
    }
    
    /**
     * Calculate available time slots for a given date
     */
    private function calculateAvailableSlots($clinic, $date)
    {
        // Get clinic settings
        $settings = ClinicAvailabilitySetting::where('clinic_id', $clinic->id)->first();
        if (!$settings) {
            return [];
        }
        
        $dayOfWeek = $date->format('l'); // Get day name (Monday, Tuesday, etc.)
        
        // Check if it's a special date
        $specialDate = ClinicSpecialDate::where('clinic_id', $clinic->id)
            ->where('date', $date->format('Y-m-d'))
            ->first();
            
        if ($specialDate && $specialDate->is_closed) {
            return []; // Clinic is closed on this special date
        }
        
        // Get operating hours
        if ($specialDate && $specialDate->start_time) {
            // Use special date hours
            $startTime = Carbon::parse($specialDate->start_time, 'Asia/Manila');
            $endTime = Carbon::parse($specialDate->end_time, 'Asia/Manila');
        } else {
            // Use regular schedule for this day of week
            $dailySchedule = ClinicDailySchedule::where('clinic_id', $clinic->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();
                
            if (!$dailySchedule) {
                // Use default settings
                $startTime = Carbon::parse($settings->default_start_time, 'Asia/Manila');
                $endTime = Carbon::parse($settings->default_end_time, 'Asia/Manila');
            } elseif ($dailySchedule->is_closed) {
                return []; // Clinic is closed on this day
            } else {
                $startTime = Carbon::parse($dailySchedule->start_time, 'Asia/Manila');
                $endTime = Carbon::parse($dailySchedule->end_time, 'Asia/Manila');
            }
        }
        
        // Get day-specific settings
        $dailySchedule = ClinicDailySchedule::where('clinic_id', $clinic->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();
        
        // Get slot duration in minutes - use day-specific if available
        $slotDuration = $settings->slot_duration;
        if ($dailySchedule && $dailySchedule->slot_duration) {
            $slotDuration = $dailySchedule->slot_duration;
        }
        
        // Generate all possible slots strictly within the operating hours
        $slots = [];
        $current = clone $startTime;
        
        while ($current->lt($endTime)) {
            $slotEnd = (clone $current)->addMinutes($slotDuration);
            
            // Ensure the ENTIRE slot (start to end) is within operating hours
            if ($slotEnd->lte($endTime)) {
                $slots[] = [
                    'start' => $current->format('H:i:s'),
                    'end' => $slotEnd->format('H:i:s'),
                    'display_time' => $current->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    'duration' => $slotDuration,
                ];
            } else {
                // Stop if adding this slot would exceed the end time
                break;
            }
            
            $current = $slotEnd;
        }
        
        // Remove slots that overlap with breaks
        $breaks = ClinicBreak::where('clinic_id', $clinic->id)
            ->where(function($query) use ($dayOfWeek) {
                $query->whereNull('day_of_week')
                      ->orWhere('day_of_week', $dayOfWeek);
            })
            ->get();
            
        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($break->start_time, 'Asia/Manila');
            $breakEnd = Carbon::parse($break->end_time, 'Asia/Manila');
            
            // Filter out slots that overlap with this break
            $slots = array_filter($slots, function($slot) use ($breakStart, $breakEnd) {
                $slotStart = Carbon::parse($slot['start'], 'Asia/Manila');
                $slotEnd = Carbon::parse($slot['end'], 'Asia/Manila');
                
                // Check if slot is entirely before break or entirely after break
                return $slotEnd->lte($breakStart) || $slotStart->gte($breakEnd);
            });
        }
        
        // Reset array keys
        $slots = array_values($slots);
        
        // Get existing non-cancelled appointments for this date
        $appointments = Appointment::where('clinic_id', $clinic->id)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->get();
        
        $bookedCount = count($appointments);
        
        \Illuminate\Support\Facades\Log::info("Found $bookedCount booked appointments for date: " . $date->format('Y-m-d'));
            
        // Get day-specific settings if available
        $dailySchedule = ClinicDailySchedule::where('clinic_id', $clinic->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();
            
        // Check if daily limit is reached - use day-specific limit if available
        $dailyLimit = $settings->daily_limit; // Default from general settings
        
        // If we have day-specific settings and daily_limit is set, use that instead
        if ($dailySchedule && $dailySchedule->daily_limit) {
            $dailyLimit = $dailySchedule->daily_limit;
        }
        
        if ($bookedCount >= $dailyLimit) {
            return []; // Fully booked
        }
        
        // Mark booked slots
        $bookedTimeSlots = [];
        foreach ($appointments as $appointment) {
            // Check if the appointment has start and end times
            if ($appointment->start_time && $appointment->end_time) {
                $bookedTimeSlots[] = [
                    'start' => Carbon::parse($appointment->start_time)->format('H:i'),
                    'end' => Carbon::parse($appointment->end_time)->format('H:i')
                ];
            }
        }
        
        // Get all booked appointment times with more detail
        $bookedAppointments = Appointment::where('clinic_id', $clinic->id)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled'])  // Ignore cancelled appointments
            ->select('appointment_time')
            ->get();
        
        // Create an array of booked times in H:i format for better matching
        $bookedSlotTimes = $bookedAppointments->map(function($appointment) {
            return Carbon::parse($appointment->appointment_time)->format('H:i');
        })->toArray();
            
        \Illuminate\Support\Facades\Log::info("Booked appointment times for date {$date}:", $bookedSlotTimes);
        
        // Mark slots as booked or available
        foreach ($slots as &$slot) {
            $isBooked = false;
            
            // Check if this slot's start time matches any booked appointment time
            if (in_array($slot['start'], $bookedSlotTimes)) {
                $isBooked = true;
            } else {
                // Also check if this slot overlaps with any booked time slots
                foreach ($bookedTimeSlots as $bookedSlot) {
                    // Check for any overlap between this slot and booked slot
                    if (($slot['start'] >= $bookedSlot['start'] && $slot['start'] < $bookedSlot['end']) || 
                        ($slot['end'] > $bookedSlot['start'] && $slot['end'] <= $bookedSlot['end']) ||
                        ($slot['start'] <= $bookedSlot['start'] && $slot['end'] >= $bookedSlot['end'])) {
                        $isBooked = true;
                        break;
                    }
                }
            }
            
            $slot['isBooked'] = $isBooked;
            $slot['status'] = $isBooked ? 'booked' : 'available';
        }
        
        // Remove already booked slots from the list
        $availableSlots = array_filter($slots, function($slot) {
            return !$slot['isBooked'];
        });
        
        return array_values($availableSlots);
    }
    
    /**
     * Get available dates for a clinic (dates when the clinic is open and has slots)
     *
     * @param  int  $clinicId
     * @return \Illuminate\Http\JsonResponse
     */

    

    
    public function getAvailableDates($clinicId)
    {
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        // Start with today's date
        $startDate = Carbon::now()->startOfDay();
        $endDate = $startDate->copy()->addDays(60); // Look ahead 60 days
        $availableDates = [];
        
        // Get all special dates in the range
        $specialDates = ClinicSpecialDate::where('clinic_id', $clinicId)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });
            
        // Get all daily schedules
        $dailySchedules = ClinicDailySchedule::where('clinic_id', $clinicId)
            ->get()
            ->keyBy('day_of_week');
            
        // Get all existing appointments to check if slots are full
        $appointments = Appointment::where('clinic_id', $clinicId)
            ->whereDate('appointment_date', '>=', $startDate)
            ->whereDate('appointment_date', '<=', $endDate)
            ->get();
            
        // Group appointments by date
        $appointmentCountByDate = [];
        foreach ($appointments as $appointment) {
            $apptDate = $appointment->appointment_date->format('Y-m-d');
            if (!isset($appointmentCountByDate[$apptDate])) {
                $appointmentCountByDate[$apptDate] = 0;
            }
            $appointmentCountByDate[$apptDate]++;
        }
        
        // Get clinic settings for daily limits
        $settings = ClinicAvailabilitySetting::where('clinic_id', $clinicId)->first();
        $defaultDailyLimit = $settings ? $settings->daily_limit : 20; // Default to 20 if not set
            
        // Iterate over the date range
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('l'); // Monday, Tuesday, etc.
            
            // Skip dates in the past
            if ($currentDate < Carbon::now()->startOfDay()) {
                $currentDate->addDay();
                continue;
            }
            
            // Default daily limit for this day
            $dailyLimit = $defaultDailyLimit;
            
            // Check if the date is available based on schedule
            $isAvailable = false;
            
            // Check if it's a special date first
            if (isset($specialDates[$dateString])) {
                $specialDate = $specialDates[$dateString];
                if (!$specialDate->is_closed) {
                    $isAvailable = true;
                    // If there's a specific daily limit for this special date
                    if ($specialDate->daily_limit) {
                        $dailyLimit = $specialDate->daily_limit;
                    }
                }
            } 
            // Otherwise check regular schedule
            else if (isset($dailySchedules[$dayOfWeek])) {
                $dailySchedule = $dailySchedules[$dayOfWeek];
                if (!$dailySchedule->is_closed) {
                    $isAvailable = true;
                    // If there's a specific daily limit for this day of week
                    if ($dailySchedule->daily_limit) {
                        $dailyLimit = $dailySchedule->daily_limit;
                    }
                }
            }
            // If no specific schedule is set, check if clinic is generally open
            else if ($clinic->is_open) {
                $isAvailable = true;
            }
            
            // Check if there are still slots available for this date
            $currentBookings = isset($appointmentCountByDate[$dateString]) ? $appointmentCountByDate[$dateString] : 0;
            $hasAvailableSlots = $currentBookings < $dailyLimit;
            
            // Only add to available dates if it's available and has slots
            if ($isAvailable && $hasAvailableSlots) {
                $availableDates[] = $dateString;
            }
            
            $currentDate->addDay();
        }
        
        return response()->json([
            'dates' => $availableDates
        ]);
    }
    
    /**
     * Get a summary of the clinic availability settings
     *
     * @param  int  $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailabilitySummary($clinicId)
    {
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        // Get availability settings
        $settings = ClinicAvailabilitySetting::where('clinic_id', $clinicId)->first();
        if (!$settings) {
            $settings = new ClinicAvailabilitySetting([
                'clinic_id' => $clinicId,
                'daily_limit' => 20,
                'slot_duration' => 30,
                'default_start_time' => '09:00',
                'default_end_time' => '17:00',
            ]);
        }
        
        // Get daily schedules
        $dailySchedules = ClinicDailySchedule::where('clinic_id', $clinicId)
            ->get()
            ->keyBy('day_of_week');
            
        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $scheduleData = [];
        
        foreach ($weekDays as $day) {
            $isDayClosed = true;
            $startTime = $settings->default_start_time;
            $endTime = $settings->default_end_time;
            
            if (isset($dailySchedules[$day])) {
                $schedule = $dailySchedules[$day];
                $isDayClosed = $schedule->is_closed;
                $startTime = $schedule->start_time;
                $endTime = $schedule->end_time;
            }
            
            $scheduleData[$day] = [
                'is_closed' => $isDayClosed,
                'start_time' => $startTime,
                'end_time' => $endTime
            ];
        }
        
        // Get next 7 days' special dates (holidays, etc.)
        $startDate = Carbon::now()->startOfDay();
        $endDate = $startDate->copy()->addDays(7);
        
        $specialDates = ClinicSpecialDate::where('clinic_id', $clinicId)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->map(function ($date) {
                return [
                    'date' => $date->date->format('Y-m-d'),
                    'is_closed' => $date->is_closed,
                    'description' => $date->description
                ];
            });
        
        return response()->json([
            'data' => [
                'is_open' => $clinic->is_open,
                'daily_limit' => $settings->daily_limit,
                'slot_duration' => $settings->slot_duration,
                'default_hours' => [
                    'start_time' => $settings->default_start_time,
                    'end_time' => $settings->default_end_time
                ],
                'schedule' => $scheduleData,
                'special_dates' => $specialDates
            ]
        ]);
    }
    
    /**
     * Get calendar dates showing available and closed days
     *
     * @param  int  $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarDates($clinicId)
    {
        try {
            $clinic = ClinicInfo::findOrFail($clinicId);
            $today = Carbon::now();
            $availableDates = [];
            $closedDates = [];
            
            // Check today and next 6 days (7 total days)
            for ($i = 0; $i < 7; $i++) {
                $checkDate = $today->copy()->addDays($i);
                $dateString = $checkDate->format('Y-m-d');
                $dayOfWeek = $checkDate->format('l'); // Monday, Tuesday, etc.
                
                // Check if it's a special date first
                $specialDate = ClinicSpecialDate::where('clinic_id', $clinicId)
                    ->where('date', $dateString)
                    ->first();
                
                if ($specialDate) {
                    if ($specialDate->is_closed) {
                        $closedDates[] = $dateString;
                    } else {
                        $availableDates[] = $dateString;
                    }
                    continue;
                }
                
                // Check weekly schedule
                $dailySchedule = ClinicDailySchedule::where('clinic_id', $clinicId)
                    ->where('day_of_week', $dayOfWeek)
                    ->first();
                
                if ($dailySchedule && $dailySchedule->is_closed) {
                    $closedDates[] = $dateString;
                } else {
                    $availableDates[] = $dateString;
                }
            }
            
            // For same-day booking policy, only return today if it's available
            // Filter out future dates since we enforce same-day booking only
            $todayString = $today->format('Y-m-d');
            if (in_array($todayString, $availableDates)) {
                $availableDates = [$todayString];
            } else {
                $availableDates = [];
                if (!in_array($todayString, $closedDates)) {
                    $closedDates[] = $todayString;
                }
            }
            
            return response()->json([
                'dates' => $availableDates,
                'closed_dates' => $closedDates
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Error in getCalendarDates: " . $e->getMessage());
            
            // Fallback - return today only if no errors in basic check
            return response()->json([
                'dates' => [Carbon::now()->format('Y-m-d')],
                'closed_dates' => []
            ]);
        }
    }
}
