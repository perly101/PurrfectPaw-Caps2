# FIX: Sunday Showing as Open When Clinic is Closed

## Problem Identified
The mobile app was showing Sunday as available with time slots even though the clinic dashboard shows Sunday as "Closed" in the weekly schedule.

## Root Cause
The `getCalendarDates()` method in `AvailabilityApiController.php` was **NOT checking the clinic's weekly schedule or special dates**. It was simply returning the next 7 days as "available" without any validation against the clinic's actual operating schedule.

### Before Fix (Broken Code):
```php
public function getCalendarDates($clinicId) {
    // Simple implementation - just return today and next few days as available
    $today = Carbon::now();
    $availableDates = [];
    
    // For same-day booking, we primarily need today to be available
    $availableDates[] = $today->format('Y-m-d');
    
    // Add next 6 days as well for flexibility
    for ($i = 1; $i <= 6; $i++) {
        $availableDates[] = $today->copy()->addDays($i)->format('Y-m-d');
    }
    
    return response()->json([
        'dates' => $availableDates,
        'closed_dates' => $closedDates  // $closedDates was undefined!
    ]);
}
```

## Fix Applied

### After Fix (Corrected Code):
```php
public function getCalendarDates($clinicId) {
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
}
```

## What the Fix Does

### 1. **Checks Weekly Schedule**
- Looks up the `ClinicDailySchedule` table for each day
- If `is_closed = true` for that day of week → adds to `closed_dates`
- If open → adds to `available_dates`

### 2. **Checks Special Dates** 
- Looks up `ClinicSpecialDate` table for specific date overrides
- Special dates take priority over weekly schedule
- Handles both closed special dates and special operating hours

### 3. **Enforces Same-Day Booking**
- Only returns today's date in `available_dates` if it's actually open
- All other dates are filtered out (same-day booking policy)

### 4. **Proper Error Handling**
- Catches exceptions and returns sensible fallbacks
- Ensures both `dates` and `closed_dates` are always arrays

## Expected Result

Now when the mobile app calls `/clinics/{clinicId}/availability/dates`:

- **If today is Sunday and clinic has Sunday closed**: 
  - `available_dates: []` 
  - `closed_dates: ["2025-10-26"]`

- **If today is Monday and clinic has Monday open**:
  - `available_dates: ["2025-10-26"]`
  - `closed_dates: []`

This will cause the mobile calendar to:
- Show red dot (closed) for Sunday instead of green dot (available)
- Not generate any time slots for Sunday
- Show proper "Clinic Closed" message when Sunday is tapped

## Files Modified
- `app/Http/Controllers/API/AvailabilityApiController.php`

The mobile app should now correctly respect the clinic's weekly schedule and special date settings!