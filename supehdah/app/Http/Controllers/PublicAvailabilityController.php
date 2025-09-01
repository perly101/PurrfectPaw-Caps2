<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicAvailabilitySetting;
use App\Models\ClinicDailySchedule;
use App\Models\ClinicInfo;
use App\Models\ClinicSpecialDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PublicAvailabilityController extends Controller
{
    /**
     * Display the public availability calendar
     */
    public function index(Request $request)
    {
        // Get clinic ID from the request or use default if not provided
        $clinicId = $request->clinic_id;
        $clinic = null;
        
        if ($clinicId) {
            $clinic = ClinicInfo::find($clinicId);
        }
        
        if (!$clinic && Auth::check()) {
            // If logged in user has a clinic, use that
            $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        }
        
        if (!$clinic) {
            // Get the first clinic as default or show not found
            $clinic = ClinicInfo::first();
            
            if (!$clinic) {
                return view('clinic.not-found');
            }
        }
        
        // Get weekly schedules
        $weeklySchedule = $this->getWeeklySchedule($clinic->id);
        
        // Get special dates for the next 3 months
        $specialDates = ClinicSpecialDate::where('clinic_id', $clinic->id)
            ->where('date', '>=', Carbon::today())
            ->where('date', '<=', Carbon::today()->addMonths(3))
            ->orderBy('date')
            ->get();
            
        // Format special dates for frontend
        $specialDatesData = [];
        foreach ($specialDates as $date) {
            $specialDatesData[] = [
                'date' => $date->date->format('Y-m-d'),
                'is_closed' => $date->is_closed,
                'start_time' => $date->is_closed ? null : Carbon::parse($date->start_time)->format('g:i A'),
                'end_time' => $date->is_closed ? null : Carbon::parse($date->end_time)->format('g:i A'),
                'description' => $date->description,
            ];
        }
        
        // Get available slots for the next month
        $availableSlots = $this->calculateAvailableSlots($clinic->id);
        
        // Format weekly schedule for frontend
        $weeklyScheduleData = [];
        foreach ($weeklySchedule as $day => $schedule) {
            $weeklyScheduleData[$day] = [
                'is_closed' => $schedule->is_closed,
                'start_time' => $schedule->is_closed ? null : Carbon::parse($schedule->start_time)->format('g:i A'),
                'end_time' => $schedule->is_closed ? null : Carbon::parse($schedule->end_time)->format('g:i A'),
                'daily_limit' => $schedule->daily_limit,
                'slot_duration' => $schedule->slot_duration,
            ];
        }
        
        return view('clinic.availability.public-calendar', [
            'clinic' => $clinic,
            'weeklySchedule' => $weeklySchedule,
            'specialDates' => $specialDates,
            'weeklyScheduleData' => $weeklyScheduleData,
            'specialDatesData' => $specialDatesData,
            'availableSlotsData' => $availableSlots,
        ]);
    }
    
    /**
     * Get weekly schedule for a clinic
     */
    private function getWeeklySchedule($clinicId)
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
     * Calculate available slots for each day in the next month
     */
    private function calculateAvailableSlots($clinicId)
    {
        $today = Carbon::today();
        $endDate = Carbon::today()->addMonths(1);
        
        // Get all appointments in the date range
        $appointments = Appointment::where('clinic_id', $clinicId)
            ->whereBetween('appointment_date', [$today, $endDate])
            ->where('status', '!=', 'cancelled')
            ->select(DB::raw('appointment_date, COUNT(*) as count'))
            ->groupBy('appointment_date')
            ->get()
            ->keyBy('appointment_date');
            
        // Get daily schedules
        $weeklySchedule = $this->getWeeklySchedule($clinicId);
        
        // Get special dates
        $specialDates = ClinicSpecialDate::where('clinic_id', $clinicId)
            ->whereBetween('date', [$today, $endDate])
            ->get()
            ->keyBy('date');
            
        // Calculate available slots for each day
        $availableSlots = [];
        $currentDate = $today->copy();
        
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('l'); // Monday, Tuesday, etc.
            
            // Check if it's a special date
            if (isset($specialDates[$dateString])) {
                $specialDate = $specialDates[$dateString];
                if (!$specialDate->is_closed) {
                    // Get daily limit from special date or from default settings
                    $dailyLimit = $specialDate->daily_limit ?? $weeklySchedule[$dayOfWeek]->daily_limit ?? 20;
                    
                    // Subtract booked appointments
                    $booked = isset($appointments[$dateString]) ? $appointments[$dateString]->count : 0;
                    $available = max(0, $dailyLimit - $booked);
                    
                    $availableSlots[$dateString] = $available;
                } else {
                    $availableSlots[$dateString] = 0; // Closed on special date
                }
            } else {
                // Check weekly schedule
                if (isset($weeklySchedule[$dayOfWeek]) && !$weeklySchedule[$dayOfWeek]->is_closed) {
                    $dailyLimit = $weeklySchedule[$dayOfWeek]->daily_limit ?? 20;
                    
                    // Subtract booked appointments
                    $booked = isset($appointments[$dateString]) ? $appointments[$dateString]->count : 0;
                    $available = max(0, $dailyLimit - $booked);
                    
                    $availableSlots[$dateString] = $available;
                } else {
                    $availableSlots[$dateString] = 0; // Closed on this day of week
                }
            }
            
            $currentDate->addDay();
        }
        
        return $availableSlots;
    }
}
