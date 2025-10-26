<?php
// Direct API test - simulate what mobile app receives

echo "=== TESTING API ENDPOINT FOR MONDAY ===\n";

// Use 2025-10-27 which should be Monday
$testDate = '2025-10-27';
$dayOfWeek = date('l', strtotime($testDate));
$clinicId = 1;

echo "Testing date: $testDate ($dayOfWeek)\n";

// Make a direct curl request to the Laravel API 
$url = "http://localhost:8000/api/clinics/$clinicId/availability/slots/$testDate";

echo "API URL: $url\n";
echo "Making request...\n";

// Use file_get_contents with context for a simple GET request
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
    echo "Please start Laravel server with: php artisan serve\n";
} else {
    echo "✅ API response received!\n";
    echo "Raw response:\n";
    echo $response . "\n";
    
    $data = json_decode($response, true);
    
    if ($data) {
        echo "\n=== PARSED RESPONSE ===\n";
        
        if (isset($data['data']['slots']) || isset($data['slots'])) {
            $slots = $data['data']['slots'] ?? $data['slots'] ?? [];
            
            echo "Number of slots: " . count($slots) . "\n";
            
            if (count($slots) > 0) {
                echo "First 5 slots:\n";
                for ($i = 0; $i < min(5, count($slots)); $i++) {
                    $slot = $slots[$i];
                    echo "  " . ($i + 1) . ". " . ($slot['display_time'] ?? $slot['start'] . ' - ' . $slot['end']) . "\n";
                }
                
                $firstSlot = $slots[0];
                if (isset($firstSlot['start']) && $firstSlot['start'] === '08:00:00') {
                    echo "\n✅ CORRECT! First slot starts at 8:00 AM\n";
                } else {
                    echo "\n❌ WRONG! First slot should start at 08:00:00 but shows: " . ($firstSlot['start'] ?? 'unknown') . "\n";
                }
            } else {
                echo "No slots returned\n";
            }
        } else {
            echo "No slots found in response\n";
            if (isset($data['data']['is_available']) && !$data['data']['is_available']) {
                echo "Reason: " . ($data['data']['message'] ?? 'Clinic closed') . "\n";
            }
        }
    } else {
        echo "Failed to parse JSON response\n";
    }
}
?>