<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClinicInfo;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestNotificationController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    /**
     * Test page for notification sound
     *
     * @return \Illuminate\View\View
     */
    public function testNotificationSound()
    {
        return view('test.notification-sound');
    }
    
    public function testAppointmentNotification(Request $request)
    {
        try {
            // Get parameters
            $clinicId = $request->input('clinic_id');
            $appointmentId = $request->input('appointment_id');
            
            // If no appointment ID is provided, get the latest one
            if (!$appointmentId) {
                $appointment = Appointment::latest()->first();
                if (!$appointment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No appointments found in the system'
                    ]);
                }
                $appointmentId = $appointment->id;
            } else {
                $appointment = Appointment::find($appointmentId);
                if (!$appointment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Appointment not found: ' . $appointmentId
                    ]);
                }
            }
            
            // If no clinic ID is provided, use the one from the appointment
            if (!$clinicId) {
                $clinicId = $appointment->clinic_id;
            }
            
            // Get the clinic
            $clinic = ClinicInfo::find($clinicId);
            if (!$clinic) {
                return response()->json([
                    'success' => false,
                    'message' => 'Clinic not found: ' . $clinicId
                ]);
            }
            
            Log::info('Testing appointment notification', [
                'clinic_id' => $clinic->id,
                'appointment_id' => $appointment->id,
                'user_id' => $clinic->user_id
            ]);
            
            // Send the notification
            $notification = $this->notificationService->notifyClinicNewAppointment($clinic, $appointment);
            
            if ($notification) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification created successfully',
                    'notification_id' => $notification->id,
                    'notification_data' => $notification->toArray()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create notification'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error in test notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}