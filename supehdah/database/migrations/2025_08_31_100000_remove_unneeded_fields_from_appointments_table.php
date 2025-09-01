<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnneededFieldsFromAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Remove unused fields
            if (Schema::hasColumn('appointments', 'pet_name')) {
                $table->dropColumn('pet_name');
            }
            
            if (Schema::hasColumn('appointments', 'pet_type')) {
                $table->dropColumn('pet_type');
            }
            
            if (Schema::hasColumn('appointments', 'breed')) {
                $table->dropColumn('breed');
            }
            
            if (Schema::hasColumn('appointments', 'treatment')) {
                $table->dropColumn('treatment');
            }
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
            // Restore the columns
            $table->string('pet_name')->nullable();
            $table->string('pet_type')->nullable();
            $table->string('breed')->nullable();
            $table->string('treatment')->nullable();
        });
    }
}
