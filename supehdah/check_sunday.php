<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=appointed', 'root', '');
$stmt = $pdo->prepare('SELECT is_closed FROM clinic_daily_schedules WHERE clinic_id = 1 AND day_of_week = ?');
$stmt->execute(['Sunday']);
$result = $stmt->fetch();
echo 'Sunday is: ' . ($result['is_closed'] ? 'CLOSED' : 'OPEN') . "\n";
?>