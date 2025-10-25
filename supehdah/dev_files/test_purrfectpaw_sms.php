<?php

use App\Services\SmsService;

// Test SMS to specific number
// Run with: php test_purrfectpaw_sms.php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $smsService = new SmsService();
    
    // Test appointment confirmation message
    $testAppointmentData = [
        'clinic_name' => 'PurrfectPaw Veterinary Clinic',
        'appointment_date' => 'October 26, 2025',
        'appointment_time' => '2:30 PM',
        'doctor_name' => 'Martinez',
        'pet_name' => 'Luna'
    ];
    
    // Phone number to test
    $testPhoneNumber = '09632879598';
    
    echo "Testing SMS Service...\n";
    echo "Sending appointment confirmation to: {$testPhoneNumber}\n";
    echo "Clinic: PurrfectPaw Veterinary Clinic\n";
    echo "Date: October 26, 2025 at 2:30 PM\n";
    echo "Doctor: Dr. Martinez\n";
    echo "Pet: Luna\n\n";
    
    $result = $smsService->sendAppointmentConfirmation($testPhoneNumber, $testAppointmentData);
    
    if ($result['success']) {
        echo "✅ SMS sent successfully!\n";
        echo "Message ID: " . ($result['data']['message_id'] ?? 'N/A') . "\n";
        echo "Status: " . ($result['data']['status'] ?? 'N/A') . "\n";
        echo "Network: " . ($result['data']['network'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Failed to send SMS:\n";
        echo "Error: " . $result['error'] . "\n";
        if (isset($result['full_response'])) {
            echo "Full Response: " . json_encode($result['full_response'], JSON_PRETTY_PRINT) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Exception occurred: " . $e->getMessage() . "\n";
}
