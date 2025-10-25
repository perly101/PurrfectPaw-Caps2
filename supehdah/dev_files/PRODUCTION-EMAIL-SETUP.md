# Production Email Setup Guide for PurrfectPaw Clinic

This guide explains how to properly configure email settings for PurrfectPaw Clinic's production environment.

## Email Configuration

For reliable email delivery in production, please configure the following in your `.env` file:

```
# Production Email Settings for Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-production-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-production-email@gmail.com
MAIL_FROM_NAME="PurrfectPaw Clinic"

# Alternative settings for other providers
# For Outlook/Office 365
# MAIL_HOST=smtp.office365.com
# MAIL_PORT=587

# For Yahoo
# MAIL_HOST=smtp.mail.yahoo.com
# MAIL_PORT=587
```

## Gmail App Passwords

If using Gmail, you'll need to:

1. Enable 2-Factor Authentication on your Google Account
2. Generate an App Password at https://myaccount.google.com/apppasswords
3. Use this App Password instead of your regular Gmail password

## Troubleshooting Email Issues

If emails are not sending in production:

1. Check your mail server logs
2. Verify that the SMTP credentials are correct
3. Ensure your hosting provider allows outbound SMTP connections
4. Check if your mail server requires additional authentication
5. Confirm that the mail server's SSL certificate is valid

## Fallback Mechanisms

The application includes multiple fallback methods for email sending:

1. Default Laravel Mail with standard SMTP
2. SwiftMailerFix service with relaxed SSL settings
3. MailService with additional configuration
4. PHP's native mail() function as a last resort

If you continue to experience issues with email delivery, please contact your hosting provider to ensure that outbound SMTP traffic is allowed.

## Testing Email Configuration

To test your email configuration:

```bash
php artisan tinker
Mail::raw('Test email', function($message) { $message->to('your-email@example.com')->subject('Test Subject'); });
```

This should send a test email and help verify your configuration is working correctly.