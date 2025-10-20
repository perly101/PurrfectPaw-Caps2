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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add billing_cycle column if it doesn't exist
            if (!Schema::hasColumn('subscriptions', 'billing_cycle')) {
                $table->string('billing_cycle')->after('amount')->nullable();
            }
            
            // Add payment_method and payment_reference if they don't exist
            if (!Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('end_date');
            }
            
            if (!Schema::hasColumn('subscriptions', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('subscriptions', 'next_billing_date')) {
                $table->dateTime('next_billing_date')->nullable()->after('end_date');
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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('subscriptions', 'billing_cycle')) {
                $table->dropColumn('billing_cycle');
            }
            
            if (Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            
            if (Schema::hasColumn('subscriptions', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
            
            if (Schema::hasColumn('subscriptions', 'next_billing_date')) {
                $table->dropColumn('next_billing_date');
            }
        });
    }
};