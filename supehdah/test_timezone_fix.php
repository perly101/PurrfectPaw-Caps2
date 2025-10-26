<?php
// Test the timezone fix

// Simulate what the API will do now with Asia/Manila timezone
date_default_timezone_set('Asia/Manila');

echo "=== TESTING TIMEZONE FIX ===\n";

// This simulates parsing 08:00:00 as Asia/Manila time
$start_time_db = '08:00:00';
$parsed_time = new DateTime($start_time_db, new DateTimeZone('Asia/Manila'));

echo "Database time: $start_time_db\n";
echo "Parsed as Asia/Manila: " . $parsed_time->format('H:i:s') . "\n";
echo "Parsed as display format: " . $parsed_time->format('g:i A') . "\n";

// Test the slot generation
echo "\nFirst few slots starting from 8:00 AM:\n";
$current = clone $parsed_time;
$slot_duration = 30; // 30 minutes

for ($i = 0; $i < 3; $i++) {
    $slot_end = clone $current;
    $slot_end->add(new DateInterval('PT' . $slot_duration . 'M'));
    
    echo "Slot " . ($i + 1) . ": " . $current->format('g:i A') . " - " . $slot_end->format('g:i A') . "\n";
    
    $current = $slot_end;
}

echo "\n✓ Should now show 8:00 AM instead of 9:00 AM in mobile app!\n";
?>