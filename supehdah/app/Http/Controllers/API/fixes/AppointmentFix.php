<?php

namespace App\Http\Controllers\API\fixes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentFix extends Controller
{
    /**
     * Fix appointments that are not showing up in the management system.
     * This will check both appointment tables and ensure data consistency.
     */
    public function fixAppointments(Request $request)
    {
        $fixCount = 0;
        
        try {
            // Check for appointments in clinic_appointments that are missing in appointments
            $clinicAppointments = DB::table('clinic_appointments')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('appointments')
                          ->whereRaw('appointments.clinic_id = clinic_appointments.clinic_id')
                          ->whereRaw('appointments.appointment_date = clinic_appointments.appointment_date')
                          ->whereRaw('appointments.appointment_time = clinic_appointments.appointment_time');
                })->get();
            
            // For each appointment in clinic_appointments but not in appointments, create a record
            foreach ($clinicAppointments as $appt) {
                DB::table('appointments')->insert([
                    'clinic_id' => $appt->clinic_id,
                    'owner_name' => $appt->owner_name,
                    'owner_phone' => $appt->owner_phone,
                    'appointment_date' => $appt->appointment_date,
                    'appointment_time' => $appt->appointment_time,
                    'status' => $appt->status,
                    'created_at' => $appt->created_at,
                    'updated_at' => now()
                ]);
                $fixCount++;
            }
            
            // Also fix any appointments that are in appointments but not clinic_appointments
            $regularAppointments = DB::table('appointments')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('clinic_appointments')
                          ->whereRaw('clinic_appointments.clinic_id = appointments.clinic_id')
                          ->whereRaw('clinic_appointments.appointment_date = appointments.appointment_date')
                          ->whereRaw('clinic_appointments.appointment_time = appointments.appointment_time');
                })->get();
            
            // For each appointment in appointments but not in clinic_appointments, create a record
            foreach ($regularAppointments as $appt) {
                DB::table('clinic_appointments')->insert([
                    'clinic_id' => $appt->clinic_id,
                    'owner_name' => $appt->owner_name,
                    'owner_phone' => $appt->owner_phone,
                    'appointment_date' => $appt->appointment_date,
                    'appointment_time' => $appt->appointment_time,
                    'status' => $appt->status,
                    'responses' => '[]',
                    'created_at' => $appt->created_at,
                    'updated_at' => now()
                ]);
                $fixCount++;
            }
            
            return response()->json([
                'status' => 'success',
                'message' => "Fixed $fixCount appointments",
                'data' => [
                    'from_clinic_appointments' => count($clinicAppointments),
                    'from_appointments' => count($regularAppointments)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fix appointments',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
