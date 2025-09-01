<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppointmentsTableAddOwnerFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop patient_name column if it exists
            if (Schema::hasColumn('appointments', 'patient_name')) {
                $table->dropColumn('patient_name');
            }
            
            // Add new columns
            if (!Schema::hasColumn('appointments', 'owner_name')) {
                $table->string('owner_name')->after('clinic_id');
            }
            
            if (!Schema::hasColumn('appointments', 'owner_phone')) {
                $table->string('owner_phone')->after('owner_name');
            }
            
            if (!Schema::hasColumn('appointments', 'status')) {
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
                      ->default('pending')
                      ->after('owner_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn(['owner_name', 'owner_phone', 'status']);
            
            // Add back patient_name if it was dropped
            $table->string('patient_name')->nullable();
        });
    }
}
