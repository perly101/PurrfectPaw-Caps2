<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionReceipt;

class SubscriptionController extends Controller
{
    /**
     * Show subscription receipt
     */
    public function showReceipt()
    {
        // Get current clinic's active subscription
        $subscription = Subscription::with('clinic.owner')
            ->where('clinic_id', auth()->user()->clinic->id)
            ->latest()
            ->first();
            
        if (!$subscription) {
            return redirect()->route('clinic.dashboard')
                ->with('error', 'No subscription found for this clinic.');
        }
        
        return view('clinic.receipt', compact('subscription'));
    }
    
    /**
     * Email the subscription receipt to the clinic owner
     */
    public function emailReceipt()
    {
        // Get current clinic's active subscription
        $subscription = Subscription::with('clinic.owner')
            ->where('clinic_id', auth()->user()->clinic->id)
            ->latest()
            ->first();
            
        if (!$subscription) {
            return redirect()->route('clinic.dashboard')
                ->with('error', 'No subscription found for this clinic.');
        }
        
        // Send email to clinic owner
        try {
            // Primary recipient is the clinic owner
            $recipient = $subscription->clinic->owner->email;
            
            // Start building the mail
            $mail = Mail::to($recipient);
            
            // Optional CC to clinic's general email if different from owner's email
            if (
                !empty($subscription->clinic->email) && 
                $subscription->clinic->email !== $subscription->clinic->owner->email
            ) {
                $mail->cc($subscription->clinic->email);
            }
            
            // Send the email
            $mail->send(new SubscriptionReceipt($subscription));
            
            // Log the email activity
            // You can uncomment this if you have a logging system
            // \Log::info('Subscription receipt emailed', [
            //     'subscription_id' => $subscription->id,
            //     'clinic_id' => $subscription->clinic_id,
            //     'recipient' => $recipient,
            //     'cc' => $cc,
            // ]);
                
            return redirect()->back()
                ->with('success', 'Receipt has been sent to your email address.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to send subscription receipt email', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscription->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to send email. Please try again later.');
        }
    }
}