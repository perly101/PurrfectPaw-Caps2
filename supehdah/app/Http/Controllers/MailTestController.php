<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Controller for testing email functionality
 */
class MailTestController extends Controller
{
    /**
     * Display email test form
     */
    public function showForm()
    {
        return view('test-email');
    }
    
    /**
     * Send a test email
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        try {
            Mail::raw('This is a test email from PurrfectPaw to verify that the email system is working correctly.', function($message) use ($request) {
                $message->to($request->email)
                    ->subject('PurrfectPaw Test Email');
            });
            
            Log::info('Test email attempt', [
                'to' => $request->email,
                'result' => 'sent'
            ]);
            
            return back()->with('status', 'Test email has been sent to ' . $request->email);
        } catch (\Exception $e) {
            Log::error('Error sending test email', [
                'error' => $e->getMessage(),
                'to' => $request->email
            ]);
            
            return back()->withErrors(['email' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }
}