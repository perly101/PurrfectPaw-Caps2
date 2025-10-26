<?php
// Test the API response directly

// Database config from Laravel .env
$host = '127.0.0.1';
$dbname = 'appointed';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TESTING API LOGIC ===\n";
    
    // Simulate what the API does for Sunday (should be closed now)
    $clinicId = 1;
    $dayOfWeek = 'Sunday';
    
    $stmt = $pdo->prepare("SELECT * FROM clinic_daily_schedules WHERE clinic_id = ? AND day_of_week = ?");
    $stmt->execute([$clinicId, $dayOfWeek]);
    $dailySchedule = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Sunday schedule: ";
    if ($dailySchedule) {
        echo ($dailySchedule['is_closed'] ? 'CLOSED' : 'OPEN');
        echo " (start: {$dailySchedule['start_time']}, end: {$dailySchedule['end_time']})\n";
    } else {
        echo "No schedule found\n";
    }
    
    // Test Monday (should be open)
    $dayOfWeek = 'Monday';
    $stmt = $pdo->prepare("SELECT * FROM clinic_daily_schedules WHERE clinic_id = ? AND day_of_week = ?");
    $stmt->execute([$clinicId, $dayOfWeek]);
    $dailySchedule = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Monday schedule: ";
    if ($dailySchedule) {
        echo ($dailySchedule['is_closed'] ? 'CLOSED' : 'OPEN');
        echo " (start: {$dailySchedule['start_time']}, end: {$dailySchedule['end_time']})\n";
    } else {
        echo "No Monday schedule - should use defaults\n";
        
        // Get default settings
        $stmt = $pdo->prepare("SELECT * FROM clinic_availability_settings WHERE clinic_id = ?");
        $stmt->execute([$clinicId]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($settings) {
            echo "Default settings: {$settings['default_start_time']} - {$settings['default_end_time']}\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>