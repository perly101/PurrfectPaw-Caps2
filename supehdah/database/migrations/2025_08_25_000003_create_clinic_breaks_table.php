<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicBreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinic_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinic_infos')->onDelete('cascade');
            $table->string('day_of_week')->nullable(); // If null, applies to all days
            $table->string('name')->default('Break'); // e.g., Lunch, Coffee, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_recurring')->default(true); // If it happens every week
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
        Schema::dropIfExists('clinic_breaks');
    }
}
