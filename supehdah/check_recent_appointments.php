<?php
// Check recent appointments to see how times are stored

$host = '127.0.0.1';
$dbname = 'appointed';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== RECENT APPOINTMENTS ANALYSIS ===\n";
    
    // Get the most recent appointment
    $stmt = $pdo->query("SELECT * FROM appointments ORDER BY created_at DESC LIMIT 3");
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($appointments)) {
        echo "No appointments found\n";
    } else {
        foreach ($appointments as $i => $appt) {
            echo "\nAppointment " . ($i + 1) . ":\n";
            echo "- ID: {$appt['id']}\n";
            echo "- Owner: {$appt['owner_name']}\n";
            echo "- Date: {$appt['appointment_date']}\n";
            echo "- Time: {$appt['appointment_time']}\n";
            echo "- Status: {$appt['status']}\n";
            echo "- Created: {$appt['created_at']}\n";
            
            // Try to analyze the time issue
            if ($appt['appointment_time']) {
                echo "- Time Analysis:\n";
                $time = $appt['appointment_time'];
                
                // If it's just a time (08:00:00), show what it means
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
                    echo "  * Stored as: $time (time only)\n";
                    $dt = new DateTime($time);
                    echo "  * Display: " . $dt->format('g:i A') . "\n";
                } else {
                    echo "  * Stored as: $time (might be datetime)\n";
                }
                
                // Check for timezone issues
                echo "  * Expected: If user selected 8:00 AM, should store 08:00:00\n";
                echo "  * Problem: If storing 18:00:00 instead, it's a UTC/timezone conversion bug\n";
            }
        }
        
        echo "\n=== DIAGNOSIS ===\n";
        echo "If appointment_time shows 18:00:00 when user selected 8:00 AM:\n";
        echo "→ This means mobile app is sending UTC time instead of Manila time\n";
        echo "→ OR Laravel is converting Manila time to UTC during save\n";
        echo "→ 8:00 AM Manila = 00:00 UTC, but 18:00:00 = 6:00 PM\n";
        echo "→ This suggests a 10-hour shift (not 8-hour), indicating other timezone issues\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>