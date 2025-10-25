# Production Environment Setup Guide

## SSL Certificate Verification Fix for Email Services

This document outlines the steps taken to fix SSL certificate verification issues in the production environment, particularly for email services using SwiftMailer.

### Problem Overview

When deploying to production, users were encountering the following error during registration:
```
ErrorException stream_socket_enable_crypto(): SSL operation failed with code 1. OpenSSL Error messages: error:1416F086:SSL routines:tls_process_server_certificate:certificate verify failed
```

This error occurs because SwiftMailer's SSL certificate verification was failing when connecting to the SMTP server (Gmail).

### Solution Implemented

We've implemented a multi-layered approach to fix these SSL certificate verification issues:

1. **Created `SwiftMailerFix` Service**
   - A dedicated service class that disables SSL certificate verification when sending emails
   - Located at: `app/Services/SwiftMailerFix.php`
   - Uses stream context options to bypass SSL certificate checks

2. **Updated Controllers**
   - Modified `RegisteredUserController` and `OtpVerificationController` to use `SwiftMailerFix`
   - Implemented a failover mechanism: tries `SwiftMailerFix` first, then `MailService`, then Laravel's Mail facade

3. **Service Providers**
   - Updated `MailConfigServiceProvider` to apply SSL fixes globally
   - Set stream context defaults at the application level

### Implementation Details

#### 1. SwiftMailerFix Service

This service provides a reliable way to send emails without SSL certificate verification issues:

```php
<?php

namespace App\Services;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Illuminate\Support\Facades\Log;

class SwiftMailerFix
{
    public static function createMailer()
    {
        // Apply global SSL fixes
        stream_context_set_default([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'security_level' => 0,
                'verify_depth' => 0,
                'disable_compression' => true,
                'SNI_enabled' => false,
                'ciphers' => 'DEFAULT:!DH'
            ]
        ]);
        
        // Create transport with SSL settings
        $transport = new Swift_SmtpTransport(
            config('mail.mailers.smtp.host'), 
            config('mail.mailers.smtp.port'),
            config('mail.mailers.smtp.encryption')
        );
        
        // Set SSL stream options
        $transport->setStreamOptions([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'security_level' => 0,
                'verify_depth' => 0,
                'disable_compression' => true,
                'SNI_enabled' => false,
                'ciphers' => 'DEFAULT:!DH'
            ]
        ]);
        
        // Set SMTP credentials
        $transport->setUsername(config('mail.mailers.smtp.username'));
        $transport->setPassword(config('mail.mailers.smtp.password'));
        
        return new Swift_Mailer($transport);
    }
    
    public static function sendMail($to, $toName, $subject, $view, $data = [])
    {
        try {
            // Apply global SSL fixes
            stream_context_set_default([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                    'security_level' => 0
                ]
            ]);
            
            // Render the view
            $htmlContent = view($view, $data)->render();
            
            // Create the Swift Mailer instance with fixes
            $mailer = self::createMailer();
            
            // Create message
            $message = new Swift_Message();
            $message->setSubject($subject);
            $message->setFrom([config('mail.from.address') => config('mail.from.name')]);
            $message->setTo([$to => $toName]);
            $message->setBody($htmlContent, 'text/html');
            
            // Send the message
            $result = $mailer->send($message);
            
            return $result > 0;
        } catch (\Exception $e) {
            Log::error('SwiftMailerFix email sending failed: ' . $e->getMessage());
            return false;
        }
    }
}
```

#### 2. Controller Updates

Both controllers now use `SwiftMailerFix` as their primary method for sending emails, with fallbacks to other methods if needed.

#### 3. Email Configuration

Make sure your `.env` file contains the correct SMTP settings:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail_account@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_gmail_account@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

> **Important**: When using Gmail, you must generate an "App Password" in your Google account settings rather than using your regular password.

### Testing the Solution

We've created test scripts to verify the solution works correctly:

1. `test_email.php` - Tests basic email sending with SSL fixes
2. `test_registration.php` - Tests user registration and OTP generation
3. `test_full_registration_flow.php` - Tests the complete registration flow with OTP verification

Run the test scripts from the project root:
```
php test_full_registration_flow.php
```

### Security Considerations

Disabling SSL certificate verification reduces security by removing validation of the SMTP server's identity. In a production environment, consider these alternatives:

1. **Install Proper SSL Certificates**: Ensure your server has up-to-date CA certificates.
2. **Specify CA Certificate Path**: Instead of disabling verification, specify the path to valid CA certificates.
3. **Use a Different Email Provider**: If SSL issues persist, consider using a different email provider or service.

### Troubleshooting

If you continue to experience email issues:

1. Check the Laravel log files at `storage/logs/laravel.log`
2. Ensure your SMTP credentials are correct in `.env`
3. Verify that your email provider allows programmatic access (e.g., Gmail requires App Passwords)
4. Try using `php artisan config:clear` to refresh configuration cache

### Maintenance Notes

This solution should be considered a temporary fix. For long-term stability:

1. Consider using a dedicated email service like SendGrid, Mailgun, or Amazon SES
2. Update to the latest SwiftMailer or switch to Symfony Mailer which has better SSL handling
3. Properly configure SSL certificate verification rather than disabling it

### Contributors

- Original fix implemented by: [Your Name]
- Last updated: [Current Date]