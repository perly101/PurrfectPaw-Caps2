<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicAvailabilitySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinic_availability_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinic_infos')->onDelete('cascade');
            $table->integer('daily_limit')->default(20); // Default 20 appointments per day
            $table->integer('slot_duration')->default(30); // Default 30 minutes per slot
            $table->time('default_start_time')->default('09:00'); // Default 9:00 AM
            $table->time('default_end_time')->default('17:00'); // Default 5:00 PM
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinic_availability_settings');
    }
}
