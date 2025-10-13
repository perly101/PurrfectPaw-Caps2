<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\ClinicInfo;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Listen for appointment creation
        Appointment::created(function (Appointment $appointment) {
            try {
                // Find the clinic associated with this appointment
                $clinic = ClinicInfo::find($appointment->clinic_id);
                
                if (!$clinic) {
                    Log::error('Failed to find clinic for appointment notification', [
                        'appointment_id' => $appointment->id,
                        'clinic_id' => $appointment->clinic_id
                    ]);
                    return;
                }
                
                // Send notification
                Log::info('Sending new appointment notification', [
                    'appointment_id' => $appointment->id,
                    'clinic_id' => $clinic->id
                ]);
                
                app(NotificationService::class)->notifyClinicNewAppointment($clinic, $appointment);
            } catch (\Exception $e) {
                Log::error('Error sending appointment notification', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }
}