<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Services\SwiftMailerFix;
use App\Models\Setting;

class EmailTestController extends Controller
{
    /**
     * Test email configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testEmail(Request $request)
    {
        // Get email settings from database for display
        $settings = Setting::getGroup('email');
        
        // Get current Laravel config settings
        $config = [
            'driver' => Config::get('mail.default'),
            'host' => Config::get('mail.mailers.smtp.host'),
            'port' => Config::get('mail.mailers.smtp.port'),
            'username' => Config::get('mail.mailers.smtp.username'),
            'encryption' => Config::get('mail.mailers.smtp.encryption'),
            'from_address' => Config::get('mail.from.address'),
            'from_name' => Config::get('mail.from.name'),
        ];
        
        // Test email (only if requested)
        $testResult = null;
        $testRecipient = $request->input('email');
        
        if ($testRecipient) {
            // Send a test email
            $success = SwiftMailerFix::sendMail(
                $testRecipient,
                'Test User',
                'Email Configuration Test',
                'emails.test-email',
                ['settings' => $settings]
            );
            
            $testResult = [
                'success' => $success,
                'message' => $success 
                    ? 'Test email sent successfully. Please check your inbox.' 
                    : 'Failed to send test email. Check server logs for details.'
            ];
        }
        
        // Return all information
        return response()->json([
            'database_settings' => $settings,
            'active_config' => $config,
            'test_result' => $testResult
        ]);
    }
}