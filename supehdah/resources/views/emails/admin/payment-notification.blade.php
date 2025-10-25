@component('mail::message')
# New Clinic Registration Payment Pending Confirmation

A new clinic has registered and claims to have made a payment that requires your confirmation.

## Clinic Details
**Name:** {{ $subscription->clinic->clinic_name }}  
**Address:** {{ $subscription->clinic->address }}  
**Contact Number:** {{ $subscription->clinic->contact_number }}  
**Owner Email:** {{ $subscription->clinic->owner->email }}

## Subscription Details
**Plan Type:** {{ ucfirst($subscription->plan_type) }}  
**Billing Cycle:** {{ ucfirst($subscription->billing_cycle) }}  
**Amount:** ₱{{ number_format($subscription->amount, 2) }}  
**Registration Date:** {{ $subscription->created_at->format('F d, Y h:i A') }}

## Payment Information
**Payment Method:** {{ ucfirst($subscription->payment_method ?? 'GCash') }}  
**GCash Reference Number:** {{ $subscription->payment_reference ?? 'Not provided' }}  
**Submitted:** {{ $subscription->updated_at->format('F d, Y h:i A') }}

@component('mail::button', ['url' => url('/admin/subscriptions/pending')])
Review Pending Payments
@endcomponent

Please verify the payment and activate the clinic account if the payment has been received.

Thanks,<br>
{{ config('app.name') }} Admin Team
@endcomponent