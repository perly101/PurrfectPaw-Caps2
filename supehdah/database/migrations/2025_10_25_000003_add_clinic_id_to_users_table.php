<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClinicIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add clinic_id as a foreign key to users table if it doesn't exist
            if (!Schema::hasColumn('users', 'clinic_id')) {
                $table->unsignedBigInteger('clinic_id')->nullable()->after('role');
                $table->foreign('clinic_id')->references('id')->on('clinic_infos')->onDelete('set null');
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
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key and column
            if (Schema::hasColumn('users', 'clinic_id')) {
                $table->dropForeign(['clinic_id']);
                $table->dropColumn('clinic_id');
            }
        });
    }
}