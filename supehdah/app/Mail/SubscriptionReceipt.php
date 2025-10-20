<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Subscription;

class SubscriptionReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The subscription instance.
     *
     * @var Subscription
     */
    public $subscription;

    /**
     * Create a new message instance.
     *
     * @param  Subscription  $subscription
     * @return void
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $clinicName = $this->subscription->clinic->clinic_name;
        $receiptNumber = $this->subscription->payment_reference ?? 'RCPT-' . $this->subscription->id;
        
        return $this
            ->subject('PurrfectPaw - Subscription Receipt #' . $receiptNumber . ' for ' . $clinicName)
            ->markdown('emails.subscription-receipt')
            ->with([
                'receiptUrl' => route('clinic.subscription.receipt'),
                'supportEmail' => 'support@purrfectpaw.com',
                'generatedAt' => now()->format('F d, Y h:i A')
            ]);
    }
}