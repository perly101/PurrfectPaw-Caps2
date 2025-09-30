<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Swift_Mailer;
use Swift_SmtpTransport;
use Swift_Message;

class MailService
{
    /**
     * Send an email using Swift Mailer directly with SSL settings
     *
     * @param string $to Recipient email
     * @param string $toName Recipient name
     * @param string $subject Email subject
     * @param string $view Email template view
     * @param array $data Data to pass to the view
     * @return bool Whether the email was sent
     */
    /**
     * Apply global SSL fixes before sending any emails
     */
    private static function applyGlobalSSLFixes()
    {
        // Disable SSL verification globally for streams
        stream_context_set_default([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'security_level' => 0, // Lowest security level
                'verify_depth' => 0,
                'disable_compression' => true,
            ]
        ]);
    }
    
    public static function sendMail($to, $toName, $subject, $view, $data = [])
    {
        // Apply global SSL fixes
        self::applyGlobalSSLFixes();
        
        try {
            // Render the view
            $htmlContent = view($view, $data)->render();
            
            // Create the Transport with explicit SSL settings
            $transport = new Swift_SmtpTransport(
                config('mail.mailers.smtp.host'), 
                config('mail.mailers.smtp.port'),
                config('mail.mailers.smtp.encryption')
            );
            
            // SSL configuration - very permissive
            $transport->setStreamOptions([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                    'security_level' => 0,
                    'verify_depth' => 0,
                    'disable_compression' => true,
                ]
            ]);
            
            $transport->setUsername(config('mail.mailers.smtp.username'));
            $transport->setPassword(config('mail.mailers.smtp.password'));
            
            // Create the Mailer using the created Transport
            $mailer = new Swift_Mailer($transport);
            
            // Create the message
            $message = new Swift_Message();
            $message->setSubject($subject);
            $message->setFrom(config('mail.from.address'), config('mail.from.name'));
            $message->setTo($to, $toName);
            $message->setBody($htmlContent, 'text/html');
            
            // Send the message
            $result = $mailer->send($message);
            
            Log::info("Email sent to {$to} with result: {$result}");
            
            return $result > 0;
        } catch (\Exception $e) {
            Log::error('Email sending failed with exception: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}