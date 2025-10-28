<?php
// Fix ALL days to have proper schedule setup

$host = '127.0.0.1';
$dbname = 'appointed';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $clinicId = 1;
    
    echo "=== SETTING UP PROPER CLINIC SCHEDULE FOR ALL DAYS ===\n";
    
    // What the clinic SHOULD have based on your description:
    // Monday-Friday: 8AM-5PM (OPEN)
    // Saturday: CLOSED  
    // Sunday: CLOSED
    
    $scheduleSetup = [
        'Monday'    => ['open' => true,  'start' => '08:00:00', 'end' => '17:00:00'],
        'Tuesday'   => ['open' => true,  'start' => '08:00:00', 'end' => '17:00:00'],
        'Wednesday' => ['open' => true,  'start' => '08:00:00', 'end' => '17:00:00'],
        'Thursday'  => ['open' => true,  'start' => '08:00:00', 'end' => '17:00:00'],
        'Friday'    => ['open' => true,  'start' => '08:00:00', 'end' => '17:00:00'],
        'Saturday'  => ['open' => false, 'start' => null,       'end' => null],
        'Sunday'    => ['open' => false, 'start' => null,       'end' => null],
    ];
    
    foreach ($scheduleSetup as $day => $config) {
        echo "\nSetting up $day: ";
        
        if ($config['open']) {
            // Create/update as OPEN day
            $sql = "INSERT INTO clinic_daily_schedules (clinic_id, day_of_week, start_time, end_time, is_closed, daily_limit, slot_duration, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, 0, 20, 30, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE 
                    start_time = VALUES(start_time),
                    end_time = VALUES(end_time),
                    is_closed = 0,
                    daily_limit = 20,
                    slot_duration = 30,
                    updated_at = NOW()";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$clinicId, $day, $config['start'], $config['end']]);
            
            echo "OPEN ({$config['start']} - {$config['end']})";
        } else {
            // Create/update as CLOSED day
            $sql = "INSERT INTO clinic_daily_schedules (clinic_id, day_of_week, start_time, end_time, is_closed, daily_limit, slot_duration, created_at, updated_at) 
                    VALUES (?, ?, NULL, NULL, 1, 20, 30, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE 
                    start_time = NULL,
                    end_time = NULL,
                    is_closed = 1,
                    updated_at = NOW()";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$clinicId, $day]);
            
            echo "CLOSED";
        }
    }
    
    echo "\n\n✅ ALL DAYS CONFIGURED!\n";
    echo "Mobile app should now show:\n";
    echo "- Monday-Friday: 8:00 AM - 5:00 PM slots\n";
    echo "- Saturday: No slots (CLOSED)\n"; 
    echo "- Sunday: No slots (CLOSED)\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>