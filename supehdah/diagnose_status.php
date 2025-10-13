<?php
// This script helps identify problematic status values in the appointments table

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get all distinct status values
$statuses = DB::select("SELECT DISTINCT status, LENGTH(status) as length FROM appointments");

echo "All statuses in the appointments table:\n";
foreach ($statuses as $status) {
    echo "Status: '{$status->status}', Length: {$status->length}\n";
}

// Specifically examine any status that's not in the allowed list
$invalidStatuses = DB::select("SELECT DISTINCT status, LENGTH(status) as length FROM appointments 
                              WHERE status NOT IN ('scheduled', 'confirmed', 'completed', 'cancelled', 'no_show') 
                              AND status IS NOT NULL");

echo "\nInvalid statuses (not in allowed list):\n";
foreach ($invalidStatuses as $status) {
    echo "Status: '{$status->status}', Length: {$status->length}, Hex: " . bin2hex($status->status) . "\n";
    
    // Check if any appointment has this status
    $count = DB::selectOne("SELECT COUNT(*) as count FROM appointments WHERE status = ?", [$status->status]);
    echo "Number of appointments with this status: {$count->count}\n";
}

// Get the current status column type
$columnType = DB::selectOne("SHOW COLUMNS FROM appointments WHERE Field = 'status'");
echo "\nCurrent status column type: " . json_encode($columnType) . "\n";

// Suggest a solution
echo "\nRecommended fix:\n";
echo "1. First update all problematic statuses to 'scheduled':\n";
foreach ($invalidStatuses as $status) {
    echo "UPDATE appointments SET status = 'scheduled' WHERE status = '" . addslashes($status->status) . "';\n";
}
echo "\n2. Then modify the column type to VARCHAR(50):\n";
echo "ALTER TABLE appointments MODIFY COLUMN status VARCHAR(50) DEFAULT 'scheduled';\n";