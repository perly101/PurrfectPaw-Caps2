<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixAppointmentsStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, let's see if there are any problematic status values
        // that don't fit into our ENUM
        if (Schema::hasTable('appointments')) {
            // Update any status values that don't match our ENUM to 'scheduled'
            DB::statement("UPDATE appointments SET status = 'scheduled' WHERE status NOT IN ('scheduled', 'confirmed', 'completed', 'cancelled', 'no_show')");
            
            // Now it's safe to modify the column
            Schema::table('appointments', function (Blueprint $table) {
                // Drop the status column and recreate it as a string
                // instead of trying to directly convert to ENUM which can cause issues
                if (Schema::hasColumn('appointments', 'status')) {
                    $table->string('status')->default('scheduled')->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // If you want to revert changes, define that here
        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                if (Schema::hasColumn('appointments', 'status')) {
                    // Revert to previous state if needed
                    DB::statement("ALTER TABLE appointments MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'scheduled'");
                }
            });
        }
    }
}
