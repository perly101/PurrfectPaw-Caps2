<?php
// Test API response for TODAY (2025-10-26) where the appointment should be

echo "=== TESTING TODAY'S SLOTS (2025-10-26) ===\n";

$testDate = '2025-10-26'; // Today - where the appointment is
$dayOfWeek = date('l', strtotime($testDate));
$clinicId = 1;

echo "Testing date: $testDate ($dayOfWeek)\n";

$url = "http://localhost:8000/api/clinics/$clinicId/availability/slots/$testDate";

echo "API URL: $url\n";
echo "Making request...\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Accept: application/json\r\n",
        'timeout' => 10
    ]
]);

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "❌ API request failed! Server might not be running.\n";
} else {
    echo "✅ API response received!\n";
    
    $data = json_decode($response, true);
    
    if ($data && isset($data['slots'])) {
        $slots = $data['slots'];
        
        echo "Number of slots: " . count($slots) . "\n";
        echo "Booked slots count: " . ($data['bookedSlots'] ?? 0) . "\n";
        echo "Booked times: " . json_encode($data['booked_times'] ?? []) . "\n\n";
        
        // Look specifically for 10:00 AM slot (should be booked)
        $tenAMSlot = null;
        foreach ($slots as $slot) {
            if ($slot['start'] === '10:00:00') {
                $tenAMSlot = $slot;
                break;
            }
        }
        
        if ($tenAMSlot) {
            echo "10:00 AM slot status:\n";
            echo "- Start: {$tenAMSlot['start']}\n";
            echo "- Display: {$tenAMSlot['display_time']}\n";
            echo "- IsBooked: " . ($tenAMSlot['isBooked'] ? 'YES' : 'NO') . "\n";
            echo "- Status: {$tenAMSlot['status']}\n";
            
            if ($tenAMSlot['isBooked']) {
                echo "✅ CORRECT: 10:00 AM slot shows as BOOKED\n";
            } else {
                echo "❌ WRONG: 10:00 AM slot should be BOOKED but shows as available\n";
            }
        } else {
            echo "❌ 10:00 AM slot not found in response\n";
        }
        
        // Show first few slots
        echo "\nFirst 5 slots:\n";
        for ($i = 0; $i < min(5, count($slots)); $i++) {
            $slot = $slots[$i];
            $status = $slot['isBooked'] ? 'BOOKED' : 'available';
            echo "  " . ($i + 1) . ". {$slot['display_time']} - $status\n";
        }
    } else {
        echo "No slots found in response or API error\n";
        echo "Raw response: $response\n";
    }
}
?>