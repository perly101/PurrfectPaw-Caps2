@component('mail::message')
# Subscription Receipt

**Receipt #**: {{ $subscription->payment_reference ?? 'RCPT-' . $subscription->id }}  
**Date**: {{ now()->format('F d, Y') }}

## Clinic Information
**{{ $subscription->clinic->clinic_name }}**  
{{ $subscription->clinic->address }}  
{{ $subscription->clinic->contact_number }}  
{{ $subscription->clinic->owner->email }}

## Subscription Details
**Plan**: {{ ucfirst($subscription->plan_type) }} Plan  
**Status**: {{ ucfirst($subscription->status) }}  
**Billing Cycle**: {{ ucfirst($subscription->billing_cycle) }}

## Subscription Period
**Start Date**: {{ $subscription->start_date ? $subscription->start_date->format('F d, Y') : 'Pending Activation' }}  
**End Date**: {{ $subscription->end_date ? $subscription->end_date->format('F d, Y') : 'Pending Activation' }}  
**Next Billing**: {{ $subscription->next_billing_date ? $subscription->next_billing_date->format('F d, Y') : 'Pending Activation' }}

## Payment Information

@component('mail::table')
| Description | Details | Amount |
| ----------- | ------- | ------:|
| {{ ucfirst($subscription->plan_type) }} Subscription | {{ ucfirst($subscription->billing_cycle) }} billing cycle | ₱{{ number_format($subscription->amount, 2) }} |
| | **Total** | **₱{{ number_format($subscription->amount, 2) }}** |
@endcomponent

## Payment Method
**Method**: {{ ucfirst($subscription->payment_method ?? 'GCash') }}  
**Reference**: {{ $subscription->payment_reference ?? 'N/A' }}

## Transaction Details
**Transaction Date**: {{ $subscription->updated_at->format('F d, Y h:i A') }}  
**Processed by**: {{ $subscription->status === 'active' ? 'Admin' : 'Pending Confirmation' }}

## Terms & Conditions
1. Subscription fees are non-refundable once activated.
2. Renewal will be processed automatically unless cancelled before the end date.
3. For cancellation or changes to your subscription, please contact our support team.
4. For any questions regarding your subscription, please email purrf3ctpaw@gmail.com

@component('mail::button', ['url' => $receiptUrl])
View Online Receipt
@endcomponent

Thank you for choosing PurrfectPaw Veterinary Management System!

If you have any questions about this receipt, please contact us at {{ $supportEmail }}.

Thanks,<br>
{{ config('app.name') }} Team

<small class="text-gray-500">This receipt was generated on {{ $generatedAt }}</small>
@endcomponent