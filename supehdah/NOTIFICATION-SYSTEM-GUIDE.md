# Global Notification System Guide

This document provides comprehensive guidance on how to use the global notification system in the Supehdah application.

## Overview

The Supehdah application implements a centralized global notification system that shows notifications at the top of the page. This system ensures users receive consistent and clear notifications across the entire application without duplicates.

## Features

- **Single Notification Display**: Notifications appear only once at the top of the page
- **Sound Notifications**: Audio alerts when new notifications arrive
- **Global Accessibility**: Works consistently across all pages
- **Customizable Settings**: Users can enable/disable sounds and control notification behavior

## How to Use the Notification System

### 1. Showing a Notification

Use the `showGlobalNotification` function to display a notification:

```javascript
showGlobalNotification({
    id: 'notification-unique-id',
    type: 'clinic_new_appointment', // or other notification type
    data: {
        title: 'Notification Title',
        body: 'Notification message text',
        // Additional data as needed
    },
    created_at: new Date().toISOString(),
    read_at: null
});
```

### 2. Playing Notification Sounds

To play a notification sound:

```javascript
playGlobalNotificationSound();
```

Users can control sound settings through the application interface or by modifying `localStorage`:

```javascript
// Enable or disable notification sounds
localStorage.setItem('notification_sound_enabled', 'true'); // or 'false'
```

### 3. Checking for New Notifications

The system automatically polls for new notifications. To manually trigger a check:

```javascript
// If the global poll function is available
if (typeof pollForNewNotifications === 'function') {
    pollForNewNotifications();
}
```

### 4. Marking Notifications as Read

To mark all notifications as read:

```javascript
// Using the API endpoint
fetch('/notifications/read-all', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('All notifications cleared');
        // Update UI as needed
    }
});
```

## Backend Integration

When creating notifications from the server side, use the following format:

```php
// Create a notification in the database
$notification = new Notification();
$notification->user_id = $userId;
$notification->type = 'clinic_new_appointment'; // or other notification type
$notification->data = [
    'title' => 'Notification Title',
    'body' => 'Notification message',
    // Other relevant data
];
$notification->save();
```

## Testing the Notification System

You can test the notification system using the test page at:
`/test/global-notification-test`

This page provides controls to:
- Test client-side notifications
- Test API-generated notifications
- Clear all notifications
- Toggle sound and popup settings
- Check the status of the notification system

## Troubleshooting

### Sound Issues

If notification sounds aren't playing:

1. Verify the sound file exists at `/public/sounds/noti.mp3`
2. Check if sound is enabled in user settings
3. Ensure the browser allows autoplay of audio
4. Run `/diagnose_sound.php` to check sound system status

### Notification Display Issues

If notifications aren't displaying properly:

1. Check browser console for errors
2. Verify the global notification container exists in the layout
3. Ensure the notification JavaScript files are properly loaded

## Best Practices

1. **Keep notifications concise**: Use clear, brief messages
2. **Include relevant actions**: When applicable, provide action links
3. **Don't overuse notifications**: Only send important information
4. **Consider user experience**: Ensure notifications enhance rather than disrupt the workflow

## Technical Details

The notification system uses the following key components:

- `global-notifications.js`: Main notification system implementation
- `notifications.js`: Legacy support and UI integration
- `global-notification-container`: DOM element where notifications are displayed
- `global-notification-sound`: Audio element for playing notification sounds

## Migrating from Legacy Notification System

If you're updating code that uses the old notification system:

1. Replace calls to `showNotificationPopup()` with `showGlobalNotification()`
2. Use the global sound function `playGlobalNotificationSound()` instead of direct audio manipulation
3. Remove any manual creation of notification containers at the bottom of the page