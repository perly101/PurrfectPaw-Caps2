@component('mail::message')
# Welcome to PurrfectPaw!

Dear {{ $clinic->clinic_name }},

Thank you for partnering with PurrfectPaw! Your clinic account has been successfully activated. You can now log in and start using our system.

## Your Clinic Details
- **Clinic Name**: {{ $clinic->clinic_name }}
- **Email**: {{ $clinic->email }}
- **Status**: Active

@component('mail::button', ['url' => route('login')])
Login Now
@endcomponent

If you have any questions or need assistance getting started, our support team is here to help.

Thanks,<br>
The PurrfectPaw Team
@endcomponent