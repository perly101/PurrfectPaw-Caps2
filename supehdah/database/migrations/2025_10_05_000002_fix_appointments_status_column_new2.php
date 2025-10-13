<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixAppointmentsStatusColumnNew2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('appointments')) {
            // Step 1: Update 'closed' status to 'completed' (which is valid in the current ENUM)
            // Using 'completed' instead of 'scheduled' since it's already in the ENUM
            DB::statement("UPDATE appointments SET status = 'completed' WHERE status = 'closed'");
            
            // Step 2: Now alter the column to change it from ENUM to VARCHAR
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'scheduled'");
            
            // Step 3: Update the allowed values to match our desired set
            // Since we've already handled 'closed' by changing it to 'completed',
            // now we need to update 'pending', 'assigned', and 'in_progress' to 'scheduled'
            DB::statement("UPDATE appointments SET status = 'scheduled' WHERE status IN ('pending', 'assigned', 'in_progress')");
            
            // At this point, all statuses should be one of: 'scheduled', 'confirmed', 'completed', 'cancelled'
            echo "Successfully updated appointment statuses and changed column type to VARCHAR(50)";
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('appointments')) {
            // Convert back to ENUM if needed
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('scheduled', 'confirmed', 'completed', 'cancelled', 'no_show') NOT NULL DEFAULT 'scheduled'");
        }
    }
}