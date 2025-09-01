<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClinicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentDebugController extends Controller
{
    /**
     * Get debug information about appointments for a clinic
     * 
     * @param int $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointmentsDebug($clinicId)
    {
        try {
            // Get all appointments for this clinic
            $appointments = Appointment::where('clinic_id', $clinicId)->get();
            
            // Count appointments by status
            $byStatus = $appointments->groupBy('status')->map->count();
            
            // Count appointments with/without date and time
            $withDate = $appointments->whereNotNull('appointment_date')->count();
            $withTime = $appointments->whereNotNull('appointment_time')->count();
            $withBoth = $appointments->filter(function($apt) {
                return !empty($apt->appointment_date) && !empty($apt->appointment_time);
            })->count();
            $withNeither = $appointments->filter(function($apt) {
                return empty($apt->appointment_date) && empty($apt->appointment_time);
            })->count();
            
            // Get the 5 most recent appointments
            $recent = $appointments->sortByDesc('created_at')->take(5)->values()->map(function($apt) {
                return [
                    'id' => $apt->id,
                    'owner_name' => $apt->owner_name,
                    'date' => $apt->appointment_date,
                    'time' => $apt->appointment_time,
                    'status' => $apt->status,
                    'created_at' => $apt->created_at
                ];
            });
            
            return response()->json([
                'status' => 'success',
                'total_count' => $appointments->count(),
                'by_status' => $byStatus,
                'with_date' => $withDate,
                'with_time' => $withTime,
                'with_both' => $withBoth,
                'with_neither' => $withNeither,
                'recent' => $recent
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug function to check and clean phantom appointments
     * 
     * @param int $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function cleanAppointments($clinicId)
    {
        // Validate clinic exists
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        // Get all appointments for this clinic
        $allAppointments = Appointment::where('clinic_id', $clinicId)->get();
        
        $validAppointments = [];
        $invalidAppointments = [];
        
        foreach ($allAppointments as $appointment) {
            // Check if this is a valid appointment (has date, time, etc.)
            $hasValidDate = !empty($appointment->appointment_date);
            $hasValidTime = !empty($appointment->appointment_time);
            $hasValidStatus = in_array($appointment->status, ['pending', 'confirmed', 'cancelled', 'completed']);
            
            if ($hasValidDate && $hasValidTime && $hasValidStatus) {
                $validAppointments[] = [
                    'id' => $appointment->id,
                    'date' => $appointment->appointment_date,
                    'time' => $appointment->appointment_time,
                    'status' => $appointment->status,
                    'owner_name' => $appointment->owner_name,
                ];
            } else {
                $invalidAppointments[] = [
                    'id' => $appointment->id,
                    'date' => $appointment->appointment_date,
                    'time' => $appointment->appointment_time,
                    'status' => $appointment->status,
                    'owner_name' => $appointment->owner_name,
                ];
                
                // Delete invalid appointment
                $appointment->delete();
            }
        }
        
        return response()->json([
            'message' => 'Debug completed',
            'total_appointments' => $allAppointments->count(),
            'valid_appointments' => count($validAppointments),
            'invalid_appointments' => count($invalidAppointments),
            'deleted_appointments' => count($invalidAppointments),
            'valid_details' => $validAppointments,
            'invalid_details' => $invalidAppointments
        ]);
    }
}
