<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clinic_homepages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->string('hero_title')->default('Welcome to our clinic');
            $table->string('hero_subtitle')->nullable();
            $table->string('hero_image')->nullable(); // storage path
            $table->text('about_text')->nullable();
            $table->string('announcement_title')->nullable();
            $table->text('announcement_body')->nullable();
            $table->string('announcement_image')->nullable(); // storage path
            $table->timestamps();

            $table->foreign('clinic_id')
                ->references('id')->on('clinic_infos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_homepages');
    }
};