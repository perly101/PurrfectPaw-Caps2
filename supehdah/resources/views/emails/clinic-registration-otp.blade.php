<x-mail::message>
# Verify Your Email for PurrfectPaw Clinic Registration

Hello {{ $user->first_name }},

Thank you for registering your clinic with PurrfectPaw! To complete your registration and proceed to payment, please verify your email address by entering the verification code below:

<x-mail::panel>
<div style="font-size: 24px; font-weight: bold; text-align: center; letter-spacing: 8px; padding: 10px;">{{ $otp }}</div>
</x-mail::panel>

This code will expire in 30 minutes for security purposes.

Once verified, you'll be able to complete your registration and set up your clinic on our platform.

<x-mail::button :url="config('app.url')">
Visit PurrfectPaw
</x-mail::button>

If you did not register for PurrfectPaw, please ignore this email.

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>