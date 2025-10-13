@extends('layouts.app')

@section('content')
<div class="container py-6">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-blue-500 text-white font-bold">
                    Global Notification System Test
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        This page tests the global notification system. Notifications should appear on any page in the application.
                    </div>
                    
                    <h4 class="mb-3">Test Controls</h4>
                    
                    <div class="mb-4">
                        <button id="test-notification-btn" class="btn btn-primary mb-2">
                            Test Global Notification
                        </button>
                        <p class="text-sm text-gray-500">Creates a test notification that appears in the global notification container</p>
                    </div>
                    
                    <div class="mb-4">
                        <button id="test-api-notification-btn" class="btn btn-success mb-2">
                            Test API Notification
                        </button>
                        <p class="text-sm text-gray-500">Creates a real notification through the API endpoint</p>
                    </div>
                    
                    <div class="mb-4">
                        <button id="clear-notifications-btn" class="btn btn-danger mb-2">
                            Clear All Notifications
                        </button>
                        <p class="text-sm text-gray-500">Marks all notifications as read</p>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h4 class="mb-3">Notification Settings</h4>
                    <div class="form-check mb-2">
                        <input type="checkbox" id="enable-sound" class="form-check-input" checked>
                        <label for="enable-sound" class="form-check-label">Enable Sound</label>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input type="checkbox" id="enable-popups" class="form-check-input" checked>
                        <label for="enable-popups" class="form-check-label">Enable Popup Notifications</label>
                    </div>
                    
                    <div class="mt-4">
                        <h4 class="mb-3">System Status</h4>
                        <div id="notification-status" class="p-3 bg-gray-100 rounded">
                            Checking notification system status...
                        </div>
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
    const statusEl = document.getElementById('notification-status');
    const testBtn = document.getElementById('test-notification-btn');
    const apiTestBtn = document.getElementById('test-api-notification-btn');
    const clearBtn = document.getElementById('clear-notifications-btn');
    const soundToggle = document.getElementById('enable-sound');
    const popupsToggle = document.getElementById('enable-popups');
    
    // Check system status
    checkNotificationSystemStatus();
    
    // Set initial toggle states
    soundToggle.checked = localStorage.getItem('notification_sound_enabled') !== 'false';
    popupsToggle.checked = localStorage.getItem('notification_popup_enabled') !== 'false';
    
    // Add event listeners
    testBtn.addEventListener('click', testGlobalNotification);
    apiTestBtn.addEventListener('click', testApiNotification);
    clearBtn.addEventListener('click', clearAllNotifications);
    
    soundToggle.addEventListener('change', function() {
        localStorage.setItem('notification_sound_enabled', soundToggle.checked);
    });
    
    popupsToggle.addEventListener('change', function() {
        localStorage.setItem('notification_popup_enabled', popupsToggle.checked);
    });
    
    /**
     * Check notification system status
     */
    function checkNotificationSystemStatus() {
        let status = '<ul class="list-disc pl-5">';
        
        // Check global notification system
        if (window.globalNotificationSystem && window.globalNotificationSystem.initialized) {
            status += '<li class="text-success">Global notification system: <strong>Active</strong></li>';
        } else {
            status += '<li class="text-danger">Global notification system: <strong>Not initialized</strong></li>';
        }
        
        // Check container
        const container = document.getElementById('global-notification-container');
        status += container 
            ? '<li class="text-success">Notification container: <strong>Found</strong></li>' 
            : '<li class="text-danger">Notification container: <strong>Missing</strong></li>';
            
        // Check audio element
        const audio = document.getElementById('global-notification-sound');
        status += audio
            ? '<li class="text-success">Notification sound: <strong>Available</strong></li>'
            : '<li class="text-danger">Notification sound: <strong>Missing</strong></li>';
            
        // Check polling
        status += window.globalNotificationSystem && window.globalNotificationSystem.pollInterval
            ? '<li class="text-success">Notification polling: <strong>Active</strong></li>'
            : '<li class="text-warning">Notification polling: <strong>Not detected</strong></li>';
            
        // Check notification count
        const count = window.globalNotificationSystem ? window.globalNotificationSystem.notificationCount : 'unknown';
        status += `<li>Current notification count: <strong>${count}</strong></li>`;
        
        status += '</ul>';
        statusEl.innerHTML = status;
    }
    
    /**
     * Test the global notification system with a client-side notification
     */
    function testGlobalNotification() {
        if (!window.globalNotificationSystem) {
            alert('Global notification system not available!');
            return;
        }
        
        const testNotification = {
            id: 'test-' + Date.now(),
            type: 'clinic_new_appointment',
            data: {
                title: 'Test Notification',
                body: 'This is a test of the global notification system',
                appointment_id: 999,
                created_at: new Date().toISOString()
            },
            created_at: new Date().toISOString(),
            read_at: null
        };
        
        // Dispatch event to show notification
        document.dispatchEvent(new CustomEvent('showNotification', {
            detail: {
                notification: testNotification
            }
        }));
        
        // Alternative direct call if available
        if (typeof showGlobalNotification === 'function') {
            showGlobalNotification(testNotification);
        }
        
        // Play sound
        if (typeof playGlobalNotificationSound === 'function') {
            playGlobalNotificationSound();
        }
        
        // Update status display
        checkNotificationSystemStatus();
    }
    
    /**
     * Test notification by creating one through the API
     */
    function testApiNotification() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('/test/appointment-notification', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('API notification created successfully:', data);
                
                // Force refresh notifications
                if (typeof pollForNewNotifications === 'function') {
                    pollForNewNotifications();
                }
            } else {
                console.error('Failed to create API notification:', data);
                alert('Failed to create notification: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error creating API notification:', error);
            alert('Error creating notification: ' + error);
        });
    }
    
    /**
     * Clear all notifications
     */
    function clearAllNotifications() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
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
                
                // Update notification count
                if (window.globalNotificationSystem) {
                    window.globalNotificationSystem.notificationCount = 0;
                    
                    // Update status display
                    checkNotificationSystemStatus();
                }
                
                // Update UI if needed
                if (typeof updatePageTitleWithNotificationCount === 'function') {
                    updatePageTitleWithNotificationCount();
                }
            } else {
                console.error('Failed to clear notifications:', data);
            }
        })
        .catch(error => {
            console.error('Error clearing notifications:', error);
        });
    }
});
</script>
@endsection