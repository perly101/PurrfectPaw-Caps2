<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableAddDoctorRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Make sure the 'role' column exists and can store 'doctor' value
            // This assumes your users table already has a 'role' column
            // If it doesn't, you would need to add it
            
            // The following command is commented because we assume the role column exists
            // $table->string('role')->default('client')->change();
            
            // Add a doctor_id column for linking to doctors table
            $table->foreignId('doctor_id')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if the column exists before trying to drop it
            if (Schema::hasColumn('users', 'doctor_id')) {
                $table->dropColumn('doctor_id');
            }
        });
    }
}