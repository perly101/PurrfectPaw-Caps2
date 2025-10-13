<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\ClinicInfo;
use App\Models\Appointment;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestNotificationController extends Controller
{
    /**
     * Test creating a notification for the clinic
     */
    public function testNotification()
    {
        $clinic = ClinicInfo::where('user_id', auth()->id())->first();
        
        if (!$clinic) {
            return response()->json(['error' => 'No clinic found for this user'], 404);
        }
        
        try {
            // Find the most recent appointment or create a test one
            $appointment = Appointment::where('clinic_id', $clinic->id)
                ->latest()
                ->first();
                
            if (!$appointment) {
                // Create a test appointment if none exists
                $appointment = new Appointment([
                    'clinic_id' => $clinic->id,
                    'owner_name' => 'Test Patient',
                    'owner_phone' => '123-456-7890',
                    'status' => 'pending',
                ]);
                $appointment->save();
                Log::info('Created test appointment', ['id' => $appointment->id]);
            }
            
            Log::info('About to create notification for clinic', [
                'clinic_id' => $clinic->id,
                'user_id' => $clinic->user_id,
                'appointment_id' => $appointment->id
            ]);
            
            // Create notification
            $notificationService = app(NotificationService::class);
            $notification = $notificationService->notifyClinicNewAppointment($clinic, $appointment);
            
            if ($notification) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification created successfully',
                    'notification' => $notification
                ]);
            } else {
                Log::warning('Notification service returned null');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create test notification - service returned null'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Test notification failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}