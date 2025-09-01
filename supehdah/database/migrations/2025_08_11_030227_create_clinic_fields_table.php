<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clinic_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id'); // references clinic_infos.id
            $table->string('label'); // e.g. "Pet"
            $table->string('type')->default('text'); // text, textarea, select, checkbox, date, time, radio, number
            $table->json('options')->nullable(); // JSON array for select/checkbox/radio options
            $table->boolean('required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('clinic_infos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_fields');
    }
};
