<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicSpecialDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinic_special_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinic_infos')->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_closed')->default(true); // If closed on this date
            $table->time('start_time')->nullable(); // If not closed but has special hours
            $table->time('end_time')->nullable(); // If not closed but has special hours
            $table->string('description')->nullable(); // e.g., "Holiday", "Staff Training"
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
        Schema::dropIfExists('clinic_special_dates');
    }
}
