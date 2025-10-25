<?php
/**
 * Email Test Script for PurrfectPaw Clinic
 * 
 * This script tests all available email sending methods to diagnose email configuration issues.
 * Run this script with: php test_email_sending.php recipient@example.com
 */

// Load Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the recipient email from command line arguments
if (!isset($argv[1]) || !filter_var($argv[1], FILTER_VALIDATE_EMAIL)) {
    echo "Usage: php test_email_sending.php recipient@example.com\n";
    echo "Please provide a valid email address as the first argument.\n";
    exit(1);
}

$recipientEmail = $argv[1];
$recipientName = "Email Test Recipient";

echo "--------------------------------------------------------------\n";
echo "EMAIL TESTING UTILITY FOR PURRFECTPAW CLINIC\n";
echo "--------------------------------------------------------------\n";
echo "Testing email sending to: " . $recipientEmail . "\n";
echo "Current environment: " . app()->environment() . "\n";
echo "Mail driver: " . config('mail.default') . "\n";
echo "Mail host: " . config('mail.mailers.smtp.host') . "\n";
echo "Mail port: " . config('mail.mailers.smtp.port') . "\n";
echo "From address: " . config('mail.from.address') . "\n\n";

// Test function
function runTest($name, $function) {
    echo "--------------------------------------------------------------\n";
    echo "TESTING METHOD: {$name}\n";
    echo "--------------------------------------------------------------\n";
    
    try {
        $startTime = microtime(true);
        $result = $function();
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        if ($result === true) {
            echo "✅ SUCCESS: {$name} completed successfully in {$executionTime}ms\n";
            return true;
        } else {
            echo "❌ FAILED: {$name} returned false\n";
            return false;
        }
    } catch (\Exception $e) {
        echo "❌ ERROR: {$name} threw an exception:\n";
        echo "   " . $e->getMessage() . "\n";
        return false;
    }
}

// 1. Test Laravel Mail
runTest("Laravel Mail", function() use ($recipientEmail, $recipientName) {
    // Apply SSL fixes for development environments
    if (app()->environment(['local', 'development', 'testing'])) {
        stream_context_set_default([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
    }
    
    \Mail::raw('This is a test email from PurrfectPaw Clinic', function ($message) use ($recipientEmail, $recipientName) {
        $message->to($recipientEmail, $recipientName)
                ->subject('Test Email from PurrfectPaw Clinic');
    });
    
    echo "Mail sent using Laravel Mail facade.\n";
    return true;
});

// 2. Test SwiftMailerFix
runTest("SwiftMailerFix", function() use ($recipientEmail, $recipientName) {
    if (!class_exists('\App\Services\SwiftMailerFix')) {
        echo "SwiftMailerFix service class not found. Skipping test.\n";
        return false;
    }
    
    $success = \App\Services\SwiftMailerFix::sendMail(
        $recipientEmail,
        $recipientName,
        'Test Email from PurrfectPaw Clinic',
        'emails.test-email',
        ['message' => 'This is a test email sent using SwiftMailerFix.']
    );
    
    if ($success) {
        echo "Mail sent using SwiftMailerFix.\n";
    }
    
    return $success;
});

// 3. Test MailService
runTest("MailService", function() use ($recipientEmail, $recipientName) {
    if (!class_exists('\App\Services\MailService')) {
        echo "MailService class not found. Skipping test.\n";
        return false;
    }
    
    $success = \App\Services\MailService::sendMail(
        $recipientEmail,
        $recipientName,
        'Test Email from PurrfectPaw Clinic',
        'emails.test-email',
        ['message' => 'This is a test email sent using MailService.']
    );
    
    if ($success) {
        echo "Mail sent using MailService.\n";
    }
    
    return $success;
});

// 4. Test PHP mail function
runTest("PHP mail()", function() use ($recipientEmail, $recipientName) {
    $subject = 'Test Email from PurrfectPaw Clinic';
    $headers = "From: " . config('mail.from.address') . "\r\n";
    $headers .= "Reply-To: " . config('mail.from.address') . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $message = "
    <html>
    <body>
        <h2>Test Email from PurrfectPaw Clinic</h2>
        <p>This is a test email sent using PHP mail() function.</p>
        <p>If you received this email, PHP mail() is working properly.</p>
    </body>
    </html>
    ";
    
    $mailSent = mail($recipientEmail, $subject, $message, $headers);
    
    if ($mailSent) {
        echo "Mail sent using PHP mail() function.\n";
    } else {
        echo "PHP mail() function returned false.\n";
    }
    
    return $mailSent;
});

// Create test template for future use
if (!file_exists(resource_path('views/emails/test-email.blade.php'))) {
    $directory = resource_path('views/emails');
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    $template = <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>Test Email</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h1 style="color: #4a5568; text-align: center;">PurrfectPaw Clinic</h1>
        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-top: 20px;">
            <h2 style="color: #2d3748;">Test Email</h2>
            <p style="color: #4a5568; line-height: 1.5;">{{ \$message ?? 'This is a test email from PurrfectPaw Clinic.' }}</p>
            <p style="color: #4a5568; line-height: 1.5;">If you received this email, the email configuration is working correctly.</p>
            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
            <p style="color: #718096; font-size: 14px; text-align: center;">© {{ date('Y') }} PurrfectPaw Clinic. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
EOT;
    
    file_put_contents(resource_path('views/emails/test-email.blade.php'), $template);
    echo "Created test email template at resources/views/emails/test-email.blade.php\n";
}

echo "\n--------------------------------------------------------------\n";
echo "EMAIL TESTS COMPLETED\n";
echo "--------------------------------------------------------------\n";
echo "If any of the methods succeeded, you should receive test emails soon.\n";
echo "Check your inbox and spam folder for emails from: " . config('mail.from.address') . "\n";
echo "--------------------------------------------------------------\n";