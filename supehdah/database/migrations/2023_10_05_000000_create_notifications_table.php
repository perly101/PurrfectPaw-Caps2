<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // doctor_assigned_patient, clinic_new_appointment, clinic_appointment_completed
            $table->morphs('notifiable'); // Polymorphic relationship (user_id/clinic_id + notifiable_type)
            $table->json('data'); // Additional data about the notification
            $table->timestamp('read_at')->nullable(); // When the notification was read
            $table->string('device_token')->nullable(); // FCM token for push notifications
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
        Schema::dropIfExists('notifications');
    }
}