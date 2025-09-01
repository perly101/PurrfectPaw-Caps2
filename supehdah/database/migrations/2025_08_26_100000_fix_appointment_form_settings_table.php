<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixAppointmentFormSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add a new allow_emergency field to the appointment form settings table
        if (Schema::hasTable('appointment_form_settings')) {
            Schema::table('appointment_form_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('appointment_form_settings', 'allow_emergency')) {
                    $table->boolean('allow_emergency')->default(false)->after('allow_attachments');
                }
                
                if (!Schema::hasColumn('appointment_form_settings', 'emergency_fee')) {
                    $table->decimal('emergency_fee', 10, 2)->nullable()->after('allow_emergency');
                }
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
        if (Schema::hasTable('appointment_form_settings')) {
            Schema::table('appointment_form_settings', function (Blueprint $table) {
                if (Schema::hasColumn('appointment_form_settings', 'allow_emergency')) {
                    $table->dropColumn('allow_emergency');
                }
                
                if (Schema::hasColumn('appointment_form_settings', 'emergency_fee')) {
                    $table->dropColumn('emergency_fee');
                }
            });
        }
    }
}
