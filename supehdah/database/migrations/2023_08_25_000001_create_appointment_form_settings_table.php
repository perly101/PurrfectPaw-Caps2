<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentFormSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_form_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->boolean('collect_owner_info')->default(true);
            $table->boolean('collect_pet_info')->default(true);
            $table->boolean('allow_notes')->default(true);
            $table->boolean('allow_attachments')->default(false);
            $table->text('terms_and_conditions')->nullable();
            $table->boolean('require_terms_agreement')->default(false);
            $table->timestamps();
            
            $table->foreign('clinic_id')->references('id')->on('clinic_infos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_form_settings');
    }
}
