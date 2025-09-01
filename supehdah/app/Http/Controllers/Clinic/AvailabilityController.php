<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClinicAvailabilitySetting;
use App\Models\ClinicBreak;
use App\Models\ClinicDailySchedule;
use App\Models\ClinicInfo;
use App\Models\ClinicSpecialDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AvailabilityController extends Controller
{
    /**
     * Display the availability settings page
     */
    public function index()
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        
        // Get or create availability settings
        $settings = ClinicAvailabilitySetting::firstOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'daily_limit' => 20,
                'slot_duration' => 30,
                'default_start_time' => '08:00',
                'default_end_time' => '17:00',
            ]
        );
        
        // Get daily schedules
        $dailySchedules = $this->getDailySchedules($clinic->id);
        
        // Get breaks
        $breaks = ClinicBreak::where('clinic_id', $clinic->id)->get();
        
        // Get special dates (holidays, vacations)
        $specialDates = ClinicSpecialDate::where('clinic_id', $clinic->id)
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->get();
        
        return view('clinic.availability.index', compact('clinic', 'settings', 'dailySchedules', 'breaks', 'specialDates'));
    }
    
    /**
     * Update general availability settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'daily_limit' => 'required|integer|min:1',
            'slot_duration' => 'required|integer|in:15,30,45,60,90,120',
            'default_start_time' => 'required|date_format:H:i',
            'default_end_time' => 'required|date_format:H:i|after:default_start_time',
        ]);
        
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        
        ClinicAvailabilitySetting::updateOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'daily_limit' => $request->daily_limit,
                'slot_duration' => $request->slot_duration,
                'default_start_time' => $request->default_start_time,
                'default_end_time' => $request->default_end_time,
            ]
        );
        
        return redirect()->route('clinic.availability.index')->with('success', 'Availability settings updated successfully.');
    }
    
    /**
     * Update daily schedule
     */
    public function updateDailySchedule(Request $request)
    {
        \Log::info('Daily Schedule Update Request', $request->all());
        
        try {
            $validated = $request->validate([
                'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'is_closed' => 'sometimes|boolean',
                'start_time' => 'required_unless:is_closed,1|nullable|date_format:H:i',
                'end_time' => 'required_unless:is_closed,1|nullable|date_format:H:i|after:start_time',
                'daily_limit' => 'nullable|integer|min:1',
                'slot_duration' => 'nullable|integer|in:15,30,45,60,90,120',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error', $e->errors());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        
        // Get default settings
        $settings = ClinicAvailabilitySetting::where('clinic_id', $clinic->id)->first();
        $defaultLimit = $settings ? $settings->daily_limit : 20;
        $defaultDuration = $settings ? $settings->slot_duration : 30;
        
        $isClosed = $request->has('is_closed');
        
        $scheduleData = [
            'is_closed' => $isClosed ? 1 : 0, // Ensure it's stored as 1 or 0 in the database
            'daily_limit' => $request->filled('daily_limit') ? $request->daily_limit : $defaultLimit,
            'slot_duration' => $request->slot_duration ?? $defaultDuration,
        ];
        
        \Log::info('Schedule data to save', $scheduleData);
        
        // Only include start/end times if not closed
        if (!$isClosed) {
            $scheduleData['start_time'] = $request->start_time;
            $scheduleData['end_time'] = $request->end_time;
        }
        
        $schedule = ClinicDailySchedule::updateOrCreate(
            [
                'clinic_id' => $clinic->id,
                'day_of_week' => $request->day_of_week,
            ],
            $scheduleData
        );
        
        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Daily schedule updated successfully.',
                'schedule' => $schedule,
                'formatted' => [
                    'start_time' => $schedule->start_time ? Carbon::parse($schedule->start_time)->format('g:i A') : null,
                    'end_time' => $schedule->end_time ? Carbon::parse($schedule->end_time)->format('g:i A') : null,
                ]
            ]);
        }
        
        // For regular requests, redirect with success message
        return redirect()->route('clinic.availability.index')->with('success', 'Daily schedule updated successfully.');
    }
    
    /**
     * Store a new break
     */
    public function storeBreak(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'day_of_week' => 'nullable|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_recurring' => 'sometimes|boolean',
        ]);
        
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        
        ClinicBreak::create([
            'clinic_id' => $clinic->id,
            'name' => $request->name,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_recurring' => $request->has('is_recurring'),
        ]);
        
        return redirect()->route('clinic.availability.index')->with('success', 'Break time added successfully.');
    }
    
    /**
     * Delete a break
     */
    public function destroyBreak($id)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        $break = ClinicBreak::where('clinic_id', $clinic->id)->findOrFail($id);
        $break->delete();
        
        return redirect()->route('clinic.availability.index')->with('success', 'Break time deleted successfully.');
    }
    
    /**
     * Store a special date (holiday, vacation, etc.)
     */
    public function storeSpecialDate(Request $request)
    {
        \Log::info('Special Date Form Submission', $request->all());
        
        try {
            $validated = $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'is_closed' => 'sometimes|boolean',
                'start_time' => 'required_unless:is_closed,1|nullable|date_format:H:i',
                'end_time' => 'required_unless:is_closed,1|nullable|date_format:H:i|after:start_time',
                'description' => 'nullable|string|max:100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Special Date Validation Error', $e->errors());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        
        // Check if this date already exists
        $existingDate = ClinicSpecialDate::where('clinic_id', $clinic->id)
            ->where('date', $request->date)
            ->first();
            
        if ($existingDate) {
            return redirect()->route('clinic.availability.index')
                ->with('error', 'This date already has special settings. Please delete the existing one first.');
        }
        
        $isClosed = $request->has('is_closed');
        
        $specialDateData = [
            'clinic_id' => $clinic->id,
            'date' => $request->date,
            'is_closed' => $isClosed ? 1 : 0,
            'description' => $request->description,
        ];
        
        \Log::info('Special date data to save', $specialDateData);
        
        // Only include start/end times if not closed
        if (!$isClosed) {
            $specialDateData['start_time'] = $request->start_time;
            $specialDateData['end_time'] = $request->end_time;
        }
        
        $specialDate = ClinicSpecialDate::create($specialDateData);
        
        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Special date added successfully.',
                'specialDate' => $specialDate,
                'formatted' => [
                    'date' => Carbon::parse($specialDate->date)->format('M d, Y'),
                    'start_time' => $specialDate->start_time ? Carbon::parse($specialDate->start_time)->format('g:i A') : null,
                    'end_time' => $specialDate->end_time ? Carbon::parse($specialDate->end_time)->format('g:i A') : null,
                ]
            ]);
        }

        return redirect()->route('clinic.availability.index')->with('success', 'Special date added successfully.');
    }
    
    /**
     * Delete a special date
     */
    public function destroySpecialDate($id)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        $specialDate = ClinicSpecialDate::where('clinic_id', $clinic->id)->findOrFail($id);
        $specialDate->delete();
        
        return redirect()->route('clinic.availability.index')->with('success', 'Special date removed successfully.');
    }
    
    /**
     * Get or initialize daily schedules for all days of the week
     */
    private function getDailySchedules($clinicId)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $existingSchedules = ClinicDailySchedule::where('clinic_id', $clinicId)->get()->keyBy('day_of_week');
        
        // Always use 8am-5pm as the default time
        $defaultStart = '08:00';
        $defaultEnd = '17:00';
        $defaultLimit = 20;
        $defaultDuration = 30;
        
        $schedules = [];
        
        foreach ($days as $day) {
            if (isset($existingSchedules[$day])) {
                $schedules[$day] = $existingSchedules[$day];
            } else {
                // Create a default schedule for this day
                $schedule = new ClinicDailySchedule();
                $schedule->day_of_week = $day;
                $schedule->start_time = $defaultStart;
                $schedule->end_time = $defaultEnd;
                $schedule->is_closed = in_array($day, ['Saturday', 'Sunday']); // Default closed on weekends
                $schedule->daily_limit = $defaultLimit;
                $schedule->slot_duration = $defaultDuration;
                $schedules[$day] = $schedule;
            }
        }
        
        return $schedules;
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);
        
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        $date = Carbon::parse($request->date);
        
        // Get the available time slots
        $slots = $this->calculateAvailableSlots($clinic, $date);
        
        return response()->json(['slots' => $slots]);
    }
    
    /**
     * Calculate available time slots for a given date
     */
    public function calculateAvailableSlots($clinic, $date)
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
            $startTime = Carbon::parse($specialDate->start_time, 'UTC');
            $endTime = Carbon::parse($specialDate->end_time, 'UTC');
        } else {
            // Use regular schedule for this day of week
            $dailySchedule = ClinicDailySchedule::where('clinic_id', $clinic->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();
                
            if (!$dailySchedule) {
                // Use default settings
                $startTime = Carbon::parse($settings->default_start_time, 'UTC');
                $endTime = Carbon::parse($settings->default_end_time, 'UTC');
            } elseif ($dailySchedule->is_closed) {
                return []; // Clinic is closed on this day
            } else {
                $startTime = Carbon::parse($dailySchedule->start_time, 'UTC');
                $endTime = Carbon::parse($dailySchedule->end_time, 'UTC');
            }
        }
        
        // Get slot duration in minutes - use day-specific duration if available
        $slotDuration = $dailySchedule && $dailySchedule->slot_duration ? $dailySchedule->slot_duration : $settings->slot_duration;
        
        // Generate all possible slots
        $slots = [];
        $current = clone $startTime;
        
        while ($current->lt($endTime)) {
            $slotEnd = (clone $current)->addMinutes($slotDuration);
            
            if ($slotEnd->lte($endTime)) {
                $slots[] = [
                    'start' => $current->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                ];
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
            $breakStart = Carbon::parse($break->start_time, 'UTC');
            $breakEnd = Carbon::parse($break->end_time, 'UTC');
            
            // Filter out slots that overlap with this break
            $slots = array_filter($slots, function($slot) use ($breakStart, $breakEnd) {
                $slotStart = Carbon::parse($slot['start'], 'UTC');
                $slotEnd = Carbon::parse($slot['end'], 'UTC');
                
                // Check if slot is entirely before break or entirely after break
                return $slotEnd->lte($breakStart) || $slotStart->gte($breakEnd);
            });
        }
        
        // Reset array keys
        $slots = array_values($slots);
        
        // Count existing appointments for this date
        $bookedCount = Appointment::where('clinic_id', $clinic->id)
            ->whereDate('appointment_date', $date)
            ->count();
            
        // Check if daily limit is reached - use day-specific limit if available
        $dailyLimit = $dailySchedule && $dailySchedule->daily_limit ? $dailySchedule->daily_limit : $settings->daily_limit;
        if ($bookedCount >= $dailyLimit) {
            return []; // Fully booked
        }
        
        // Get already booked slots
        $bookedSlots = Appointment::where('clinic_id', $clinic->id)
            ->whereDate('appointment_date', $date)
            ->pluck('appointment_time')
            ->map(function($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();
            
        // Remove already booked slots
        $availableSlots = array_filter($slots, function($slot) use ($bookedSlots) {
            return !in_array($slot['start'], $bookedSlots);
        });
        
        return array_values($availableSlots);
    }
}
