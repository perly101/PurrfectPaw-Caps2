<?php
// Check ALL clinic schedule settings vs what should appear in mobile

$host = '127.0.0.1';
$dbname = 'appointed';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $clinicId = 1;
    
    echo "=== CLINIC DASHBOARD SETTINGS vs MOBILE APP EXPECTATIONS ===\n\n";
    
    // 1. Check default settings (fallback)
    $stmt = $pdo->prepare("SELECT * FROM clinic_availability_settings WHERE clinic_id = ?");
    $stmt->execute([$clinicId]);
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "DEFAULT SETTINGS (used when no day-specific schedule):\n";
    if ($settings) {
        echo "- Start Time: {$settings['default_start_time']}\n";
        echo "- End Time: {$settings['default_end_time']}\n";
        echo "- Slot Duration: {$settings['slot_duration']} minutes\n";
        echo "- Daily Limit: {$settings['daily_limit']} appointments\n";
    } else {
        echo "- No default settings found!\n";
    }
    
    echo "\n=== DAY-SPECIFIC SCHEDULES ===\n";
    
    // 2. Check each day of the week
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    foreach ($days as $day) {
        $stmt = $pdo->prepare("SELECT * FROM clinic_daily_schedules WHERE clinic_id = ? AND day_of_week = ?");
        $stmt->execute([$clinicId, $day]);
        $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "\n$day:\n";
        if ($schedule) {
            $status = $schedule['is_closed'] ? 'CLOSED' : 'OPEN';
            echo "  Status: $status\n";
            if (!$schedule['is_closed']) {
                echo "  Hours: {$schedule['start_time']} to {$schedule['end_time']}\n";
                echo "  Slot Duration: {$schedule['slot_duration']} minutes\n";
                echo "  Daily Limit: {$schedule['daily_limit']} appointments\n";
                
                // Calculate expected first few slots
                $start = new DateTime($schedule['start_time']);
                $duration = $schedule['slot_duration'];
                echo "  Expected First 3 Slots:\n";
                for ($i = 0; $i < 3; $i++) {
                    $end = clone $start;
                    $end->add(new DateInterval("PT{$duration}M"));
                    echo "    " . ($i + 1) . ". " . $start->format('g:i A') . " - " . $end->format('g:i A') . "\n";
                    $start = $end;
                }
            }
        } else {
            echo "  No specific schedule (should use DEFAULT SETTINGS)\n";
            if ($settings && !$settings['default_start_time']) {
                echo "  ⚠️  WARNING: No default settings to fall back to!\n";
            } else if ($settings) {
                echo "  Should show: {$settings['default_start_time']} to {$settings['default_end_time']}\n";
            }
        }
    }
    
    echo "\n=== SUMMARY FOR MOBILE APP ===\n";
    echo "Mobile app should show EXACTLY what's configured above.\n";
    echo "If a day has no specific schedule, use the DEFAULT SETTINGS.\n";
    echo "If a day is CLOSED, show NO slots.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>