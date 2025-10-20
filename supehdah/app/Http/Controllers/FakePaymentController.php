<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Clinic;
use App\Models\Subscription;
use App\Mail\ClinicActivated;
use Carbon\Carbon;

class FakePaymentController extends Controller
{
    /**
     * Show the payment page with QR code
     */
    public function show(Request $request)
    {
        // Check if there's a subscription in session
        if (!$request->session()->has('subscription_id')) {
            return redirect()->route('landing')
                ->with('error', 'No pending subscription found. Please start the registration process again.');
        }
        
        try {
            // Get the subscription
            $subscription = Subscription::with('clinic')->findOrFail($request->session()->get('subscription_id'));
            
            // Check if subscription is already paid
            if ($subscription->status !== 'pending') {
                return redirect()->route('payment.thank-you');
            }
            
            return view('clinic.payment', compact('subscription'));
            
        } catch (\Exception $e) {
            Log::error('Payment page error: ' . $e->getMessage());
            
            return redirect()->route('landing')
                ->with('error', 'An error occurred. Please try again.');
        }
    }
    
    /**
     * Process the payment confirmation from the user
     */
    public function process(Request $request)
    {
        // Validate the subscription ID
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id'
        ]);
        
        try {
            // Start transaction
            DB::beginTransaction();
            
            // Get the subscription
            $subscription = Subscription::with('clinic.owner')->findOrFail($request->subscription_id);
            
            // Update subscription status to pending admin confirmation
            $subscription->status = 'pending_admin_confirmation';
            $subscription->payment_method = 'gcash';
            $subscription->payment_reference = 'GCASH-' . time();
            $subscription->save();
            
            // Update clinic status to pending admin confirmation
            $clinic = $subscription->clinic;
            $clinic->status = 'pending_admin_confirmation';
            $clinic->save();
            
            // Send notification to admin
            $adminEmail = config('mail.admin_email', 'admin@purrfectpaw.com');
            Mail::to($adminEmail)->send(new \App\Mail\AdminPaymentNotification($subscription));
            
            // Let the user know their payment is being processed
            if ($clinic->owner) {
                // You can create a new mail class for this notification if needed
                // Mail::to($clinic->owner->email)->send(new PaymentPendingConfirmation($clinic));
            }
            
            // Commit transaction
            DB::commit();
            
            // Clear the subscription ID from session
            $request->session()->forget('subscription_id');
            
            return redirect()->route('payment.thank-you.pending');
            
        } catch (\Exception $e) {
            // Roll back transaction on error
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred during payment processing. Please try again.');
        }
    }
    
    /**
     * Admin confirmation of a payment
     */
    public function adminConfirm(Request $request, $id)
    {
        // Check if user is admin
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            // Start transaction
            DB::beginTransaction();
            
            // Get the subscription
            $subscription = Subscription::with('clinic.owner')->findOrFail($id);
            
            // Update subscription status and dates
            $now = Carbon::now();
            $subscription->status = 'active';
            $subscription->start_date = $now;
            
            // Set end date based on billing cycle
            if ($subscription->billing_cycle === 'monthly') {
                $subscription->end_date = $now->copy()->addMonth();
                $subscription->next_billing_date = $now->copy()->addMonth();
            } else {
                $subscription->end_date = $now->copy()->addYear();
                $subscription->next_billing_date = $now->copy()->addYear();
            }
            
            $subscription->save();
            
            // Update clinic status
            $clinic = $subscription->clinic;
            $clinic->status = 'active';
            $clinic->save();
            
            // Send welcome email
            if ($clinic->owner) {
                Mail::to($clinic->owner->email)->send(new ClinicActivated($clinic));
            }
            
            // Commit transaction
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Clinic payment confirmed and account activated successfully.');
            
        } catch (\Exception $e) {
            // Roll back transaction on error
            DB::rollBack();
            Log::error('Admin payment confirmation error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred during payment confirmation. Please try again.');
        }
    }
    
    /**
     * Show the thank you page after successful payment and activation
     */
    public function thankYou()
    {
        try {
            // Get the latest active subscription
            $subscription = Subscription::with('clinic.owner')
                ->where('status', 'active')
                ->latest()
                ->firstOrFail();
                
            return view('clinic.thank-you', compact('subscription'));
        } catch (\Exception $e) {
            Log::error('Thank you page error: ' . $e->getMessage());
            return redirect()->route('landing')
                ->with('error', 'Unable to retrieve subscription information.');
        }
    }
    
    /**
     * Show the thank you page after payment is submitted but pending admin confirmation
     */
    public function thankYouPending()
    {
        try {
            // Get the latest pending admin confirmation subscription
            $subscription = Subscription::with('clinic.owner')
                ->where('status', 'pending_admin_confirmation')
                ->latest()
                ->firstOrFail();
                
            return view('clinic.thank-you-pending', compact('subscription'));
        } catch (\Exception $e) {
            Log::error('Thank you pending page error: ' . $e->getMessage());
            return redirect()->route('landing')
                ->with('error', 'Unable to retrieve subscription information.');
        }
    }
}