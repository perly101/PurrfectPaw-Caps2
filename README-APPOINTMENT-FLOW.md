# Appointment Booking Flow

This document describes the appointment booking flow in the clinic management application.

## Flow Overview

1. User selects a clinic (from AppointmentsScreen)
2. User opens the calendar screen and sees:
   - Available dates (marked with green dots)
   - Closed dates (marked with red dots) - holidays, weekends, etc.
3. User selects an available date and sees available time slots
4. User selects a time slot and is taken to the appointment form (BookAppointmentScreen)
5. User completes the form with required details and submits
6. The slot becomes unavailable for other users

## Key Components

### Backend (Laravel)

- `CalendarApiController.php` - Handles API endpoints for calendar functionality
  - `getAvailabilityDates()` - Returns available and closed dates
  - `getAvailableSlotsForDate()` - Returns available time slots for a selected date

### Mobile App (React Native)

- `ClinicCalendarScreen.tsx` - Displays calendar with available/closed dates
- `BookAppointmentScreen.tsx` - Handles appointment form and submission
- `calendarApi.ts` - API client for calendar functionality

## Navigation Flow

1. From `ClinicCalendarScreen`, when a time slot is selected, the app navigates to `BookAppointment` (stack screen)
2. The `BookAppointmentScreen` receives:
   - `clinicId` - ID of the selected clinic
   - `clinicName` - Name of the selected clinic
   - `date` - Selected date in YYYY-MM-DD format
   - `timeSlot` - Selected time slot object

## Date and Time Handling

- All date and time operations use the Philippines timezone (UTC+8)
- Dates are stored in YYYY-MM-DD format
- Times are stored in HH:MM:SS format

## Clinic Configuration (Dashboard)

- Clinic owners can configure:
  - Weekly schedule (which days and times they're open)
  - Special dates (holidays or different hours)
  - Custom form fields for patients to complete
