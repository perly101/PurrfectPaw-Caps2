<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\AppointmentFieldValue;
use App\Models\ClinicInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixAppointmentDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:fix-dates {clinic_id?} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix appointments with missing date/time information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clinicId = $this->argument('clinic_id');
        $fixAll = $this->option('all');

        // Build query based on parameters
        $query = Appointment::query();
        
        if ($clinicId) {
            $query->where('clinic_id', $clinicId);
            $this->info("Fixing appointments for clinic ID: $clinicId");
        } elseif ($fixAll) {
            $this->info("Fixing appointments for ALL clinics");
        } else {
            $this->error("Please specify a clinic ID or use --all to fix all clinics");
            return 1;
        }

        // Find appointments with missing date/time info
        $appointments = $query->where(function($q) {
            $q->whereNull('appointment_date')
              ->orWhereNull('appointment_time');
        })->get();

        $count = $appointments->count();
        $this->info("Found $count appointments with missing date/time information");

        // Ask for confirmation if there are many appointments
        if ($count > 10 && !$this->confirm("Do you want to continue and fix $count appointments?")) {
            $this->info("Operation cancelled");
            return 0;
        }

        // Process each appointment
        $fixed = 0;
        $failed = 0;
        foreach ($appointments as $appointment) {
            try {
                // Get custom values for this appointment
                $customValues = AppointmentFieldValue::where('appointment_id', $appointment->id)
                    ->with('field')
                    ->get();

                // Look for date/time related fields in custom values
                $foundDate = null;
                $foundTime = null;
                foreach ($customValues as $value) {
                    $fieldLabel = strtolower($value->field->label ?? '');
                    
                    // Check for date related fields
                    if (str_contains($fieldLabel, 'date')) {
                        $foundDate = $value->value;
                    }
                    // Check for time related fields
                    elseif (str_contains($fieldLabel, 'time')) {
                        $foundTime = $value->value;
                    }
                }
                
                // If not found in custom values, use created_at date as fallback
                if (!$foundDate) {
                    $foundDate = $appointment->created_at->format('Y-m-d');
                }
                
                // If still no time found, use a default time
                if (!$foundTime) {
                    $foundTime = '09:00:00';
                }

                // Update the appointment with the found date/time
                $appointment->appointment_date = $foundDate;
                $appointment->appointment_time = $foundTime;
                $appointment->save();

                $this->line("Fixed appointment #{$appointment->id} for {$appointment->owner_name}: Date={$foundDate}, Time={$foundTime}");
                $fixed++;
            } catch (\Exception $e) {
                $this->error("Failed to fix appointment #{$appointment->id}: " . $e->getMessage());
                Log::error("Failed to fix appointment #{$appointment->id}: " . $e->getMessage());
                $failed++;
            }
        }

        // Summary
        $this->newLine();
        $this->info("Summary:");
        $this->info("- Fixed: $fixed appointments");
        $this->info("- Failed: $failed appointments");
        
        return 0;
    }
}
