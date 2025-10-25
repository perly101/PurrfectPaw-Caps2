<?php

/**
 * Admin Email Receipt Test Script
 * This script tests the ability for admins to send subscription receipts to clinics
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionReceipt;

// Autoload the Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Admin Email Receipt Test...\n\n";

try {
    // Step 1: Find a subscription with 'active' status
    echo "Finding an active subscription...\n";
    $subscription = Subscription::with('clinic.owner')
        ->where('status', 'active')
        ->latest()
        ->first();
        
    if (!$subscription) {
        echo "No active subscription found. Test cannot continue.\n";
        exit(1);
    }
    
    echo "Found subscription #{$subscription->id} for clinic: {$subscription->clinic->clinic_name}\n";
    echo "Clinic owner email: {$subscription->clinic->owner->email}\n\n";
    
    // Step 2: Test sending the email
    echo "Attempting to send email...\n";
    
    // Create a new instance of the mailable
    $mailable = new SubscriptionReceipt($subscription);
    
    // Get the rendered content to check
    $renderedView = $mailable->render();
    
    echo "Email template rendered successfully. Content length: " . strlen($renderedView) . " bytes\n";
    
    // Test sending the email (using a fake mailer for testing)
    Mail::fake();
    Mail::to($subscription->clinic->owner->email)->send($mailable);
    
    // Verify the email was sent
    Mail::assertSent(SubscriptionReceipt::class, function ($mail) use ($subscription) {
        return $mail->hasTo($subscription->clinic->owner->email);
    });
    
    echo "Email sent successfully!\n";
    
    // Step 3: Output the email structure for verification
    echo "\nEmail Structure:\n";
    echo "- Subject: " . $mailable->subject . "\n";
    echo "- From: " . config('mail.from.address') . "\n";
    echo "- To: " . $subscription->clinic->owner->email . "\n";
    echo "- Template: emails.subscription-receipt\n";
    echo "- Includes subscription details for plan: " . ucfirst($subscription->plan_type) . "\n";
    echo "- Includes payment reference: " . ($subscription->payment_reference ?? 'RCPT-' . $subscription->id) . "\n";
    
    echo "\nTest completed successfully!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
