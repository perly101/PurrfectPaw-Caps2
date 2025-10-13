<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixAppointmentsStatusColumnNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, check if the appointments table exists
        if (Schema::hasTable('appointments')) {
            // Check if there are any statuses not in our allowed list
            $invalidStatuses = DB::select("SELECT DISTINCT status FROM appointments WHERE status NOT IN ('scheduled', 'confirmed', 'completed', 'cancelled', 'no_show') AND status IS NOT NULL");
            
            if (count($invalidStatuses) > 0) {
                // Log the problematic statuses for debugging
                foreach ($invalidStatuses as $status) {
                    echo "Found invalid status: " . $status->status . "\n";
                }
                
                // First, try to update row by row instead of a batch update
                foreach ($invalidStatuses as $status) {
                    // Double check the length to diagnose the truncation issue
                    $currentStatus = $status->status;
                    echo "Updating status: " . $currentStatus . " (length: " . strlen($currentStatus) . ")\n";
                    
                    // Update rows with this status to 'scheduled' - doing it one by one
                    DB::statement("UPDATE appointments SET status = 'scheduled' WHERE status = ?", [$currentStatus]);
                }
            } else {
                echo "No invalid status values found.\n";
            }
            
            // Now check if we need to modify the column structure
            $columnType = DB::select("SHOW COLUMNS FROM appointments WHERE Field = 'status'");
            echo "Current column type: " . json_encode($columnType) . "\n";
            
            // If it's not already a VARCHAR, modify it
            if (!empty($columnType) && strpos(strtoupper($columnType[0]->Type), 'VARCHAR') === false) {
                echo "Converting status column to VARCHAR(50)...\n";
                DB::statement("ALTER TABLE appointments MODIFY COLUMN status VARCHAR(50) DEFAULT 'scheduled'");
            } else {
                echo "Status column is already VARCHAR, no need to change structure.\n";
            }
        } else {
            echo "Appointments table does not exist.\n";
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // If you need to revert changes
        if (Schema::hasTable('appointments')) {
            echo "No action needed for rollback.\n";
        }
    }
}