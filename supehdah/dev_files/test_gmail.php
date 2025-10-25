<?php
/**
 * Quick Gmail Test Script
 * 
 * This script tests sending an email through Gmail using your specific configuration.
 * Run this directly with: php test_gmail.php your@email.com
 */

// Check if an email address was provided
if (!isset($argv[1]) || !filter_var($argv[1], FILTER_VALIDATE_EMAIL)) {
    echo "Usage: php test_gmail.php recipient@example.com\n";
    echo "Please provide a valid email address as the first argument.\n";
    exit(1);
}

$recipient = $argv[1];

// Gmail credentials - directly from your .env file
$username = 'purrf3ctpaw@gmail.com';
$password = 'btfsddqawibpjkni'; // App password from your .env
$fromName = 'PurrfectPaw';

echo "Testing Gmail SMTP with:\n";
echo "Username: $username\n";
echo "Password: [HIDDEN]\n";
echo "Sending to: $recipient\n\n";

// Generate a test code
$testCode = rand(100000, 999999);

// Configure the mailer
$transport = new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');
$transport->setUsername($username);
$transport->setPassword($password);
$transport->setStreamOptions([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

// Create the mailer
try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    $mailer = new Swift_Mailer($transport);
    $message = new Swift_Message('PurrfectPaw Test Email');
    $message->setFrom([$username => $fromName]);
    $message->setTo([$recipient]);
    
    // Simple message body
    $message->setBody("
        <html>
        <body>
            <h2>PurrfectPaw Email Test</h2>
            <p>This is a test email from PurrfectPaw system.</p>
            <p>Your test verification code is: <strong>{$testCode}</strong></p>
            <p>If you received this email, your Gmail configuration is working correctly!</p>
        </body>
        </html>
    ", 'text/html');
    
    echo "Attempting to send email...\n";
    $result = $mailer->send($message);
    
    if ($result) {
        echo "SUCCESS! Email was sent to $recipient\n";
        echo "Check your inbox (and spam folder) for the test email with code: $testCode\n";
    } else {
        echo "FAILED! Email was not sent.\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "STACK TRACE: " . $e->getTraceAsString() . "\n";
}