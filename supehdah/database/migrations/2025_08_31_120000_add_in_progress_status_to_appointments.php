<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddInProgressStatusToAppointments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For MySQL, we need to modify the enum values
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'assigned', 'confirmed', 'in_progress', 'completed', 'closed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
        // For SQLite, which doesn't support enum types
        else {
            // For SQLite, we'll just rely on application-level validation
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // For MySQL, revert back to previous enum values
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'assigned', 'confirmed', 'completed', 'closed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
        // For SQLite, do nothing as it doesn't enforce enum constraints
    }
}
