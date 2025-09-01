<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicInfo;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TestAppointmentController extends Controller
{
    /**
     * Create a test appointment to verify date/time handling
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTestAppointment(Request $request)
    {
        // Only allow in non-production environments
        if (app()->environment('production')) {
            return response()->json(['message' => 'Test appointments are not allowed in production'], 403);
        }

        $clinic = ClinicInfo::first();
        
        if (!$clinic) {
            return response()->json(['message' => 'No clinic found in the system'], 404);
        }
        
        // Create an appointment with today's date and a set time
        $today = Carbon::now('Asia/Manila')->format('Y-m-d');
        $testTime = '14:30:00'; // 2:30 PM
        
        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'owner_name' => 'Test User',
            'owner_phone' => '09123456789',
            'appointment_date' => $today,
            'appointment_time' => $testTime,
            'status' => 'pending',
        ]);
        
        return response()->json([
            'message' => 'Test appointment created successfully',
            'appointment' => [
                'id' => $appointment->id,
                'clinic_id' => $appointment->clinic_id,
                'owner_name' => $appointment->owner_name,
                'appointment_date' => $appointment->appointment_date,
                'appointment_date_raw' => $appointment->getAttributes()['appointment_date'],
                'appointment_time' => $appointment->appointment_time,
                'appointment_time_raw' => $appointment->getAttributes()['appointment_time'],
                'status' => $appointment->status,
                'created_at' => $appointment->created_at,
            ]
        ]);
    }
}
