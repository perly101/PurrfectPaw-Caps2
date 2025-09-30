<?php

/**
 * Email Verification Test Script
 * 
 * This script tests whether only the OTP email is sent during registration,
 * without sending the Laravel default verification email.
 * 
 * Run this script from the Laravel root directory:
 * php test_email_verification.php
 */

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\EmailVerificationOtp;
use App\Services\SwiftMailerFix;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "=== Email Verification Test ===\n\n";

// Test email (use a real email to actually test delivery)
$testEmail = 'test_user_' . time() . '@example.com';
$testPassword = 'password123';

// Step 1: Create test user
echo "Step 1: Creating test user with email: {$testEmail}\n";

try {
    // Delete user if already exists
    User::where('email', $testEmail)->delete();
    
    // Create new user
    $user = User::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => $testEmail,
        'phone_number' => '1234567890',
        'password' => Hash::make($testPassword),
        'role' => 'user',
    ]);
    
    echo "✓ User created successfully with ID: {$user->id}\n";
} catch (\Exception $e) {
    echo "✗ Error creating user: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Trigger the Registered event (this should NOT send the default verification email)
echo "\nStep 2: Triggering Registered event\n";
try {
    event(new Registered($user));
    echo "✓ Registered event triggered successfully\n";
} catch (\Exception $e) {
    echo "✗ Error triggering Registered event: " . $e->getMessage() . "\n";
}

// Step 3: Generate and send OTP manually
echo "\nStep 3: Generating and sending OTP\n";
try {
    // Generate OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Delete any existing OTP
    EmailVerificationOtp::where('user_id', $user->id)->delete();
    
    // Create OTP record
    $otpRecord = EmailVerificationOtp::create([
        'user_id' => $user->id,
        'email' => $user->email,
        'otp' => $otp,
        'expires_at' => Carbon::now()->addMinutes(30),
    ]);
    
    echo "✓ OTP generated: {$otp}\n";
    
    // Send OTP via email
    $name = $user->first_name . ' ' . $user->last_name;
    
    $success = SwiftMailerFix::sendMail(
        $user->email,
        $name,
        'Email Verification OTP',
        'emails.otp-verification',
        ['user' => $user, 'otp' => $otp]
    );
    
    if ($success) {
        echo "✓ OTP email sent successfully\n";
    } else {
        echo "✗ Failed to send OTP email\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error in OTP process: " . $e->getMessage() . "\n";
}

// Display test summary
echo "\n=== Test Summary ===\n";
echo "User ID: " . $user->id . "\n";
echo "Email: " . $user->email . "\n";
echo "Only OTP should be sent, without the default Laravel verification email.\n";
echo "Check your email inbox to confirm only the OTP email was received.\n";
echo "Test completed!\n";

// Clean up (optional)
if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] === '--cleanup') {
    echo "\nCleaning up test data...\n";
    $user->delete();
    echo "Test user deleted.\n";
}

?>