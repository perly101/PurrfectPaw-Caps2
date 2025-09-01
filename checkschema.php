<?php

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the Laravel autoloader from the project
require_once __DIR__ . '/supehdah/vendor/autoload.php';
require_once __DIR__ . '/supehdah/bootstrap/app.php';

// Get the database connection
$db = app('db')->connection();

// Check if the columns exist
$hasColumns = $db->getSchemaBuilder()->hasColumns('clinic_daily_schedules', ['daily_limit', 'slot_duration']);

echo "Checking clinic_daily_schedules table structure:\n";

if ($hasColumns) {
    echo "✅ Both 'daily_limit' and 'slot_duration' columns exist\n";
    
    // Get all columns for the table
    $columns = $db->getSchemaBuilder()->getColumnListing('clinic_daily_schedules');
    echo "All columns in the table:\n";
    print_r($columns);
    
} else {
    echo "❌ One or both columns are missing\n";
    
    // Show which columns are missing
    $columns = $db->getSchemaBuilder()->getColumnListing('clinic_daily_schedules');
    echo "Existing columns:\n";
    print_r($columns);
    
    echo "\nChecking individual columns:\n";
    echo "daily_limit: " . ($db->getSchemaBuilder()->hasColumn('clinic_daily_schedules', 'daily_limit') ? "exists" : "missing") . "\n";
    echo "slot_duration: " . ($db->getSchemaBuilder()->hasColumn('clinic_daily_schedules', 'slot_duration') ? "exists" : "missing") . "\n";
}

// Check structure of clinic_special_dates table too
echo "\nChecking clinic_special_dates table structure:\n";
$specialDateColumns = $db->getSchemaBuilder()->getColumnListing('clinic_special_dates');
echo "All columns in the table:\n";
print_r($specialDateColumns);
