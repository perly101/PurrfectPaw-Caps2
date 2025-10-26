<?php
// Test API response for Monday to see if slot times are correct

$host = '127.0.0.1';
$dbname = 'appointed';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $clinicId = 1;
    
    echo "=== TESTING MONDAY SLOT TIMES ===\n";
    
    // Check what Monday should show
    $stmt = $pdo->prepare("SELECT * FROM clinic_daily_schedules WHERE clinic_id = ? AND day_of_week = 'Monday'");
    $stmt->execute([$clinicId]);
    $mondaySchedule = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($mondaySchedule) {
        echo "DATABASE: Monday is configured as:\n";
        echo "- Start Time: {$mondaySchedule['start_time']}\n";
        echo "- End Time: {$mondaySchedule['end_time']}\n";
        echo "- Slot Duration: {$mondaySchedule['slot_duration']} minutes\n";
        echo "- Status: " . ($mondaySchedule['is_closed'] ? 'CLOSED' : 'OPEN') . "\n\n";
        
        if (!$mondaySchedule['is_closed']) {
            // Calculate what slots SHOULD be generated
            echo "EXPECTED SLOTS (what mobile should show):\n";
            
            $start = new DateTime($mondaySchedule['start_time']);
            $end = new DateTime($mondaySchedule['end_time']);
            $duration = $mondaySchedule['slot_duration'];
            
            $current = clone $start;
            $slotNumber = 1;
            
            while ($current < $end && $slotNumber <= 6) { // Show first 6 slots
                $slotEnd = clone $current;
                $slotEnd->add(new DateInterval("PT{$duration}M"));
                
                if ($slotEnd <= $end) {
                    echo "  Slot $slotNumber: " . $current->format('g:i A') . " - " . $slotEnd->format('g:i A') . "\n";
                } else {
                    break;
                }
                
                $current = $slotEnd;
                $slotNumber++;
            }
        }
    } else {
        echo "No Monday schedule found - should use default settings\n";
    }
    
    echo "\n=== WHAT MOBILE APP SHOULD RECEIVE ===\n";
    echo "The mobile app should get slots starting exactly at the configured time.\n";
    echo "If clinic sets 8:00 AM - 5:00 PM, first slot should be 8:00 AM - 8:30 AM\n";
    echo "NOT 9:00 AM or any other time!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>