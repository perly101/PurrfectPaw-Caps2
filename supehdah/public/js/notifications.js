// Notifications module - modified to work with global notification system
document.addEventListener('DOMContentLoaded', function() {
    // Check if we need to initialize local notifications (for backward compatibility)
    if (document.querySelector('.clinic-notifications')) {
        initializeNotifications();
    }
    
    // Initialize notification listeners for page-specific elements
    initNotificationUIElements();
});

function initializeNotifications() {
    // Function to show notification popup - exposing globally for use in settings page
    window.showNotificationPopup = function(notification) {
        // Check if popups are enabled
        const popupsEnabled = localStorage.getItem('notification_popup_enabled') !== 'false';
        if (!popupsEnabled) return;
        
        // Create popup element
        const popup = document.createElement('div');
        popup.className = 'notification-popup';
        
        // Determine icon based on notification type
        let icon = '';
        if (notification.type === 'clinic_new_appointment') {
            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>';
        } else if (notification.type === 'clinic_appointment_completed') {
            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
        } else {
            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" /></svg>';
        }
        
        // Create popup content
        const title = notification.data.title || 'New Notification';
        const body = notification.data.body || '';
        let actionLink = '';
        
        if (notification.type === 'clinic_new_appointment' && notification.data.appointment_id) {
            actionLink = `<a href="/clinic/appointments/${notification.data.appointment_id}" class="text-blue-600 hover:text-blue-800 text-sm">View Appointment</a>`;
        }
        
        // Set popup HTML
        popup.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl overflow-hidden max-w-md w-full border border-gray-200 animate-slide-in">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-4 py-2 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="text-white">
                            ${icon}
                        </div>
                        <h3 class="ml-2 text-white font-medium">New Notification</h3>
                    </div>
                    <button class="text-white hover:text-gray-200 close-notification">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <h4 class="font-semibold text-gray-900">${title}</h4>
                    <p class="text-sm text-gray-600 mt-1">${body}</p>
                    <div class="mt-3 flex justify-between items-center">
                        ${actionLink}
                        <span class="text-xs text-gray-500">Just now</span>
                    </div>
                </div>
            </div>
        `;
        
        // Create notifications container if it doesn't exist
        let container = document.getElementById('notifications-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notifications-container';
            container.className = 'fixed top-5 right-5 z-50 space-y-3';
            document.body.appendChild(container);
        }
        
        // Add popup to container
        container.appendChild(popup);
        
        // Add close event
        popup.querySelector('.close-notification').addEventListener('click', function() {
            popup.classList.add('animate-fade-out');
            setTimeout(() => {
                popup.remove();
            }, 300);
        });
        
        // Auto close after 8 seconds
        setTimeout(() => {
            if (popup.parentNode) {
                popup.classList.add('animate-fade-out');
                setTimeout(() => {
                    if (popup.parentNode) {
                        popup.remove();
                    }
                }, 300);
            }
        }, 8000);
    }
    
    // Function to play notification sound - use the global one if available
    function playNotificationSound() {
        // If the global notification system is available, use that
        if (window.globalNotificationSystem && typeof playGlobalNotificationSound === 'function') {
            playGlobalNotificationSound();
            return;
        }
        
        // Otherwise use the legacy sound system
        // Check if sound is enabled in settings
        const soundEnabled = localStorage.getItem('notification_sound_enabled') !== 'false';
        if (!soundEnabled) return;
        
        console.log('Playing notification sound (legacy)...');
        
        // Try to use the enhanced notification sound
        if (typeof window.playNotificationSound === 'function') {
            window.playNotificationSound('standard');
        } else {
            // Fallback to loading the MP3 directly
            console.log('Fallback: Direct audio play');
            const audio = new Audio('/sounds/noti.mp3');
            audio.volume = 1.0; // Full volume
            audio.play().catch(e => {
                console.error('Could not play notification sound', e);
                // Try once more with user interaction
                document.addEventListener('click', function playOnce() {
                    new Audio('/sounds/noti.mp3').play();
                    document.removeEventListener('click', playOnce);
                }, { once: true });
            });
        }
    }
    
    // Poll for new notifications - now using the global system if available
    function pollNotifications() {
        // If the global notification system is handling polling, don't duplicate
        if (window.globalNotificationSystem && window.globalNotificationSystem.pollInterval) {
            console.log('Using global notification polling system');
            return;
        }
        
        const lastCheckedTime = localStorage.getItem('lastNotificationCheck') || new Date(0).toISOString();
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Fetch new notifications
        fetch('/check-new-notifications?since=' + encodeURIComponent(lastCheckedTime), {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.notifications && data.notifications.length > 0) {
                // Update notification list in sidebar without refresh
                updateNotificationsList();
                
                // Show notifications using global or legacy system
                data.notifications.forEach(notification => {
                    // Try to use global notification system first
                    if (window.globalNotificationSystem && typeof showGlobalNotification === 'function') {
                        // Use global notification system (top notifications only)
                        showGlobalNotification(notification);
                    } else {
                        // Fall back to the legacy system
                        // But don't show this if we're already showing with global system
                        showNotificationPopup(notification);
                    }
                });
                
                // Play sound once for all notifications
                playNotificationSound();
            }
            
            // Update last check time
            if (data.now) {
                localStorage.setItem('lastNotificationCheck', data.now);
            } else {
                localStorage.setItem('lastNotificationCheck', new Date().toISOString());
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
    }
    
    // Function to update notifications list without refreshing the page
    function updateNotificationsList() {
        const notificationsArea = document.querySelector('.clinic-notifications');
        
        if (notificationsArea) {
            fetch('/clinic/notifications-component')
                .then(response => response.text())
                .then(html => {
                    notificationsArea.outerHTML = html;
                })
                .catch(error => console.error('Error updating notifications list:', error));
        }
    }
    
    // Start polling - only if global system isn't handling it
    if (!window.globalNotificationSystem || !window.globalNotificationSystem.pollInterval) {
        console.log('Using legacy notification polling');
        setInterval(pollNotifications, 15000); // Check every 15 seconds
        
        // Initial check
        pollNotifications();
    }
}

/**
 * Initialize notification UI elements like counters and badges
 */
function initNotificationUIElements() {
    // Find notification count badges and update them
    updateNotificationCountUI();
    
    // Add event listeners to notification-related UI elements
    setupNotificationUIListeners();
}

/**
 * Update notification count badges in the UI
 */
function updateNotificationCountUI() {
    // Find any notification count badges in the current page
    const countBadges = document.querySelectorAll('.notification-count-badge');
    
    if (countBadges.length > 0) {
        // Fetch the current count from the server
        fetch('/clinic/notification-count', {
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count !== undefined) {
                // Store count in global system if available
                if (window.globalNotificationSystem) {
                    window.globalNotificationSystem.notificationCount = data.count;
                }
                
                // Update all badges
                countBadges.forEach(badge => {
                    badge.textContent = data.count;
                    badge.classList.toggle('hidden', data.count === 0);
                });
            }
        })
        .catch(error => console.error('Error fetching notification count:', error));
    }
}

/**
 * Set up event listeners for notification UI elements
 */
function setupNotificationUIListeners() {
    // Mark all as read buttons
    const markAllReadButtons = document.querySelectorAll('.mark-all-notifications-read');
    markAllReadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            markAllNotificationsAsRead();
        });
    });
    
    // Individual mark as read buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.mark-notification-read')) {
            e.preventDefault();
            const button = e.target.closest('.mark-notification-read');
            const notificationId = button.dataset.id;
            if (notificationId) {
                markNotificationAsRead(notificationId);
            }
        }
    });
}

/**
 * Mark all notifications as read
 */
function markAllNotificationsAsRead() {
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
            // Update UI
            updateNotificationCountUI();
            
            // Refresh notifications list if present
            updateNotificationsList();
            
            // Update global count if available
            if (window.globalNotificationSystem) {
                window.globalNotificationSystem.notificationCount = 0;
                updatePageTitleWithNotificationCount(); // From global-notifications.js
            }
        }
    })
    .catch(error => console.error('Error marking all notifications as read:', error));
}

// Add styles for notifications
document.head.insertAdjacentHTML('beforeend', `
<style>
    @keyframes slideIn {
        0% { transform: translateX(100%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes fadeOut {
        0% { opacity: 1; }
        100% { opacity: 0; }
    }
    
    .animate-slide-in {
        animation: slideIn 0.4s ease forwards;
    }
    
    .animate-fade-out {
        animation: fadeOut 0.3s ease forwards;
    }
</style>
`);