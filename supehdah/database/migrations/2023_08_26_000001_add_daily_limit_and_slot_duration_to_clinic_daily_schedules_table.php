<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDailyLimitAndSlotDurationToClinicDailySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_daily_schedules', function (Blueprint $table) {
            $table->integer('daily_limit')->nullable()->after('is_closed');
            $table->integer('slot_duration')->nullable()->after('daily_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_daily_schedules', function (Blueprint $table) {
            $table->dropColumn(['daily_limit', 'slot_duration']);
        });
    }
}
