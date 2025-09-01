<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppointmentsTableAddPetDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('pet_name')->nullable()->after('appointment_time');
            $table->string('pet_type')->nullable()->after('pet_name');
            $table->string('breed')->nullable()->after('pet_type');
            $table->string('treatment')->nullable()->after('breed');
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
            $table->dropColumn('pet_name');
            $table->dropColumn('pet_type');
            $table->dropColumn('breed');
            $table->dropColumn('treatment');
        });
    }
}
