<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=appointed', 'root', '');
    $stmt = $pdo->query('SELECT id, appointment_date, appointment_time, owner_name, status FROM appointments ORDER BY created_at DESC LIMIT 5');
    
    echo "Recent appointments:\n";
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['id']}, Date: {$row['appointment_date']}, Time: {$row['appointment_time']}, Owner: {$row['owner_name']}, Status: {$row['status']}\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>