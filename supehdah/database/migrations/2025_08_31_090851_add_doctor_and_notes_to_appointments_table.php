<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDoctorAndNotesToAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Add doctor_id as foreign key
            $table->foreignId('doctor_id')->nullable()->after('clinic_id')->constrained('doctors')->nullOnDelete();
            
            // Add consultation notes field
            $table->text('consultation_notes')->nullable()->after('status');
            
            // Update status enum to include new statuses
            // First, drop the existing enum constraint
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropColumn('status');
            }
        });
        
        // For SQLite compatibility, we're splitting this into two operations
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('appointments', function (Blueprint $table) {
                // Re-add status with updated enum values
                $table->enum('status', [
                    'pending', 
                    'assigned', 
                    'confirmed', 
                    'completed', 
                    'closed', 
                    'cancelled'
                ])->default('pending')->after('owner_phone');
            });
        } else {
            // For SQLite, we'll handle this differently
            DB::statement("ALTER TABLE appointments ADD COLUMN new_status VARCHAR(255) NOT NULL DEFAULT 'pending'");
            DB::statement("UPDATE appointments SET new_status = status");
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            Schema::table('appointments', function (Blueprint $table) {
                $table->renameColumn('new_status', 'status');
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
        Schema::table('appointments', function (Blueprint $table) {
            // Drop the doctor_id foreign key
            $table->dropForeign(['doctor_id']);
            $table->dropColumn(['doctor_id', 'consultation_notes']);
            
            // Reset status enum to original values
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropColumn('status');
            }
        });
        
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('appointments', function (Blueprint $table) {
                // Re-add status with original enum values
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
                      ->default('pending')->after('owner_phone');
            });
        } else {
            // SQLite version
            DB::statement("ALTER TABLE appointments ADD COLUMN new_status VARCHAR(255) NOT NULL DEFAULT 'pending'");
            DB::statement("UPDATE appointments SET new_status = status");
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            Schema::table('appointments', function (Blueprint $table) {
                $table->renameColumn('new_status', 'status');
            });
        }
    }
}
