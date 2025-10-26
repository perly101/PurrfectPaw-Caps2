<?php
// Test the API response for Sunday directly

echo "=== TESTING SUNDAY API RESPONSE ===\n";

// Database connection
$host = '127.0.0.1';
$dbname = 'appointed';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $clinicId = 1;
    
    // Get a Sunday date (like the one shown in mobile: October 2025)
    // Let's use 2025-10-26 which should be a Sunday
    $testDate = '2025-10-26';
    $dayOfWeek = date('l', strtotime($testDate));
    
    echo "Test date: $testDate\n";
    echo "Day of week: $dayOfWeek\n\n";
    
    // Check daily schedule
    $stmt = $pdo->prepare("SELECT * FROM clinic_daily_schedules WHERE clinic_id = ? AND day_of_week = ?");
    $stmt->execute([$clinicId, $dayOfWeek]);
    $dailySchedule = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Daily schedule found: " . ($dailySchedule ? 'YES' : 'NO') . "\n";
    if ($dailySchedule) {
        echo "is_closed: " . $dailySchedule['is_closed'] . "\n";
        echo "start_time: " . $dailySchedule['start_time'] . "\n";
        echo "end_time: " . $dailySchedule['end_time'] . "\n";
    }
    
    // Check special date
    $stmt = $pdo->prepare("SELECT * FROM clinic_special_dates WHERE clinic_id = ? AND date = ?");
    $stmt->execute([$clinicId, $testDate]);
    $specialDate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nSpecial date found: " . ($specialDate ? 'YES' : 'NO') . "\n";
    if ($specialDate) {
        echo "is_closed: " . $specialDate['is_closed'] . "\n";
    }
    
    // Simulate API logic
    echo "\n=== API LOGIC SIMULATION ===\n";
    $shouldBeClosed = ($specialDate && $specialDate['is_closed']) || ($dailySchedule && $dailySchedule['is_closed']);
    echo "Should be closed: " . ($shouldBeClosed ? 'YES' : 'NO') . "\n";
    
    if ($shouldBeClosed) {
        echo "API should return: empty slots array\n";
    } else {
        echo "API should return: available slots\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>