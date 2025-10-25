# SMS Integration with Semaphore API

This document describes the SMS notification feature implemented for appointment confirmations and cancellations.

## Overview

The system automatically sends SMS notifications to patients when:
- A doctor confirms an appointment
- An appointment is cancelled 
- Status is updated via clinic dashboard, doctor dashboard, or mobile API

## Configuration

### Environment Variables

Add the following variables to your `.env` file:

```env
# SMS Configuration (Semaphore)
SMS_API_KEY=6dff29a20c4ad21b0ff30725e15c23d0
SMS_SENDER_NAME=AutoRepair
SMS_ENABLED=true
```

- `SMS_API_KEY`: Your Semaphore API key
- `SMS_SENDER_NAME`: The name that appears as the sender (max 11 characters)
- `SMS_ENABLED`: Set to `false` to disable SMS sending (for development/testing)

## Usage

### Automatic SMS Triggers

SMS notifications are automatically sent when appointment status changes to:

1. **Confirmed**: Sends appointment confirmation with details
2. **Cancelled**: Sends cancellation notification

### Supported Controllers

The SMS integration is implemented in:

1. **Clinic Dashboard**: `App\Http\Controllers\Clinic\AppointmentController`
2. **Doctor Dashboard**: `App\Http\Controllers\Doctor\AppointmentController` 
3. **Mobile API**: `App\Http\Controllers\API\AppointmentApiController`

### Message Format

**Confirmation Message:**
```
Good day! Your appointment at [Clinic Name] has been CONFIRMED.

Details:
Date: [Date]
Time: [Time]
Doctor: Dr. [Doctor Name]
Pet: [Pet Name]

Please arrive on time to avoid complications. Thank you!
```

**Cancellation Message:**
```
Your appointment at [Clinic Name] on [Date] at [Time] has been CANCELLED. Please contact us to reschedule. Thank you.
```

## Phone Number Format

The system automatically formats phone numbers to Philippine format:
- Converts `09XXXXXXXX` to `639XXXXXXXX`
- Adds country code if missing
- Removes non-numeric characters

## Error Handling

- SMS failures are logged but don't prevent appointment status updates
- All SMS attempts are logged for debugging
- SSL verification is disabled for development environments

## Testing

Use the included test script to verify SMS functionality:

```bash
php test_sms.php
```

**Note**: Update the phone number in the test script to your actual number for testing.

## API Response Handling

The service handles these Semaphore API response statuses as successful:
- `Queued`
- `Pending` 
- `Sent`

## Production Considerations

1. **SSL Certificates**: Remove `verify => false` option in production
2. **Rate Limits**: Be aware of Semaphore API rate limits
3. **Error Monitoring**: Monitor SMS logs for delivery issues
4. **Cost Management**: SMS sends cost money - monitor usage

## Troubleshooting

### Common Issues

1. **SSL Certificate Errors**
   - Temporarily disabled for development
   - Ensure proper SSL certificates in production

2. **Invalid Phone Numbers**
   - System auto-formats Philippine numbers
   - Invalid numbers will be logged as errors

3. **API Key Issues**
   - Verify your Semaphore API key is correct
   - Check account balance and status

4. **Message Length**
   - Long messages may be split into multiple parts
   - Each part counts as a separate SMS charge

### Logs

SMS activities are logged in Laravel's standard log files:
- Successful sends: `Log::info()`
- Errors: `Log::error()`
- Disabled mode: `Log::info()`

Check `storage/logs/laravel.log` for SMS-related entries.

## Development vs Production

### Development Mode
- Set `SMS_ENABLED=false` to disable actual SMS sending
- Messages will be logged instead of sent
- No SMS charges incurred

### Production Mode  
- Set `SMS_ENABLED=true` to enable SMS sending
- Monitor logs for delivery confirmation
- Ensure sufficient Semaphore account balance

## Support

For Semaphore API issues:
- Visit: https://semaphore.co/
- Documentation: https://semaphore.co/docs
- Support: Contact Semaphore support team