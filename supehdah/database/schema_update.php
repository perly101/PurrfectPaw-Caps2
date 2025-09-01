<?php
/**
 * Database schema update script for clinic_daily_schedules table
 * 
 * This script adds daily_limit and slot_duration columns to the clinic_daily_schedules table
 * Run this script directly by navigating to its URL or from the command line
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Check if columns exist first
$hasColumns = false;
if (Schema::hasTable('clinic_daily_schedules')) {
    $columns = Schema::getColumnListing('clinic_daily_schedules');
    if (in_array('daily_limit', $columns) && in_array('slot_duration', $columns)) {
        $hasColumns = true;
    }
}

if ($hasColumns) {
    echo "Columns already exist. No changes needed.\n";
} else {
    try {
        Schema::table('clinic_daily_schedules', function (Blueprint $table) {
            $table->integer('daily_limit')->nullable()->after('is_closed');
            $table->integer('slot_duration')->nullable()->after('daily_limit');
        });
        echo "Successfully added daily_limit and slot_duration columns to clinic_daily_schedules table.\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
