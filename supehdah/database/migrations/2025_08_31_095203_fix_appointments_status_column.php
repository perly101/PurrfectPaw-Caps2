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
        // We need to use DB statements directly to modify the enum values
        if (DB::getDriverName() === 'mysql') {
            // For MySQL, we'll use ALTER TABLE to modify the enum column
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'assigned', 'confirmed', 'completed', 'closed', 'cancelled') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            // Revert back to original enum values
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending'");
        }
    }
}
