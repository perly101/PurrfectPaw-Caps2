<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_infos', function (Blueprint $table) {
            // Add owner_id column if it doesn't exist
            if (!Schema::hasColumn('clinic_infos', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('user_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('clinic_infos', 'status')) {
                $table->string('status')->default('pending')->after('contact_number');
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
        Schema::table('clinic_infos', function (Blueprint $table) {
            // Drop foreign key constraint
            if (Schema::hasColumn('clinic_infos', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->dropColumn('owner_id');
            }
            
            // Drop status column
            if (Schema::hasColumn('clinic_infos', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};