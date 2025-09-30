<?php

namespace App\Services;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Illuminate\Support\Facades\Log;

class SwiftMailerFix
{
    /**
     * Create a Swift Mailer instance with SSL fixes applied.
     *
     * @return Swift_Mailer
     */
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
        
        // Create mailer with fixed transport
        $mailer = new Swift_Mailer($transport);
        
        // Log the transport setup
        Log::info('SwiftMailer transport created with SSL verification disabled');
        
        return $mailer;
    }
    
    /**
     * Send an email using the fixed Swift Mailer.
     *
     * @param string $to Recipient email
     * @param string $toName Recipient name
     * @param string $subject Email subject
     * @param string $view Email template view
     * @param array $data Data to pass to the view
     * @return bool Whether the email was sent
     */
    public static function sendMail($to, $toName, $subject, $view, $data = [])
    {
        try {
            // Apply global SSL fixes before anything else
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
            
            // Set php.ini settings for SSL
            ini_set('openssl.cafile', '');
            ini_set('openssl.capath', '');
            
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
            
            Log::info("Email sent to {$to} with result: {$result}");
            
            return $result > 0;
        } catch (\Exception $e) {
            Log::error('SwiftMailerFix email sending failed: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}