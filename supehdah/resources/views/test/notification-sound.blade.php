@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Test Notification Sound</div>

                <div class="card-body">
                    <div class="alert alert-info">
                        This page allows you to test the notification sound system.
                    </div>
                    
                    <div class="mb-4">
                        <h4>Notification Sound Status</h4>
                        <p id="sound-status">Checking status...</p>
                    </div>
                    
                    <div class="mb-4">
                        <button id="test-sound-btn" class="btn btn-primary">Test Notification Sound</button>
                        <button id="test-notification-btn" class="btn btn-success ml-2">Test Full Notification</button>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Sound Settings</h4>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="enable-sound" checked>
                            <label class="form-check-label" for="enable-sound">
                                Enable notification sounds
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4>Sound File Information</h4>
                        <p>Current sound file: <code>/sounds/noti.mp3</code></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if notification sound system is initialized
        const status = document.getElementById('sound-status');
        
        if (window.notificationSoundSystemInitialized) {
            status.textContent = 'Notification sound system is active and ready.';
            status.className = 'text-success';
        } else {
            status.textContent = 'Notification sound system is not initialized properly!';
            status.className = 'text-danger';
        }
        
        // Test sound button
        document.getElementById('test-sound-btn').addEventListener('click', function() {
            console.log('Testing notification sound...');
            
            // First try using the global function
            if (typeof window.playNotificationSound === 'function') {
                window.playNotificationSound();
                console.log('Played sound using global function');
            } else {
                // Fallback
                console.log('Using fallback sound method');
                const audio = new Audio('/sounds/noti.mp3');
                audio.play().catch(e => {
                    console.error('Could not play sound:', e);
                    alert('Failed to play sound. Check browser autoplay settings.');
                });
            }
        });
        
        // Test full notification
        document.getElementById('test-notification-btn').addEventListener('click', function() {
            // Create a test notification
            const testNotification = {
                id: 'test-' + Date.now(),
                type: 'clinic_new_appointment',
                data: {
                    title: 'Test Notification',
                    body: 'This is a test notification with sound',
                    created_at: new Date().toISOString()
                },
                created_at: new Date().toISOString(),
                read_at: null
            };
            
            // Show a notification using the global system if available
            if (window.globalNotificationSystem && typeof showGlobalNotification === 'function') {
                showGlobalNotification(testNotification);
            } 
            // Don't use the popup notification system as we only want the top notification
            // Removed: else if (typeof showNotificationPopup === 'function') {
            //    showNotificationPopup(testNotification);
            // }
            else {
                // Simple alert as fallback
                alert('Test notification: ' + testNotification.data.body);
            }
            
            // Play notification sound
            if (typeof playNotificationSound === 'function') {
                playNotificationSound();
            }
        });
        
        // Sound enable/disable toggle
        const soundToggle = document.getElementById('enable-sound');
        
        // Set initial state from localStorage
        soundToggle.checked = localStorage.getItem('notification_sound_enabled') !== 'false';
        
        // Update localStorage when changed
        soundToggle.addEventListener('change', function() {
            localStorage.setItem('notification_sound_enabled', soundToggle.checked);
            console.log('Notification sound ' + (soundToggle.checked ? 'enabled' : 'disabled'));
        });
    });
</script>
@endsection