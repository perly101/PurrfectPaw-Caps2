// Enhanced notification sound JavaScript file
document.addEventListener('DOMContentLoaded', function() {
    console.log('Enhanced notification sound system initializing...');
    
    // Function to create and initialize notification sounds
    function initNotificationSounds() {
        try {
            // Use the custom noti.mp3 sound file
            const standardNotification = new Audio('/sounds/noti.mp3');
            standardNotification.id = 'standard-notification-sound';
            standardNotification.preload = 'auto'; // Preload the sound for faster playback
            
            const urgentNotification = new Audio('/sounds/noti.mp3'); // Using same sound for both types
            urgentNotification.id = 'urgent-notification-sound';
            urgentNotification.preload = 'auto';
            
            // Append audio elements to the document (invisible)
            standardNotification.style.display = 'none';
            urgentNotification.style.display = 'none';
            document.body.appendChild(standardNotification);
            document.body.appendChild(urgentNotification);
            
            // Make sure audio is loaded
            standardNotification.load();
            urgentNotification.load();
            
            // Expose global functions to play sounds
            window.playNotificationSound = function(type = 'standard') {
                try {
                    // Check if sound is enabled in settings
                    const soundEnabled = localStorage.getItem('notification_sound_enabled') !== 'false';
                    if (!soundEnabled) {
                        console.log('Notification sound is disabled in settings');
                        return;
                    }
                    
                    console.log('Playing notification sound: ' + type);
                    
                    // Select the appropriate sound based on type
                    let soundElement;
                    if (type === 'urgent') {
                        soundElement = document.getElementById('urgent-notification-sound');
                    } else {
                        soundElement = document.getElementById('standard-notification-sound');
                    }
                    
                    // Try to play the sound
                    if (soundElement) {
                        // Reset to beginning in case it's already playing
                        soundElement.currentTime = 0;
                        
                        // Play the sound with proper error handling
                        const playPromise = soundElement.play();
                        
                        // Handle promise rejection (happens in some browsers)
                        if (playPromise !== undefined) {
                            playPromise.catch(error => {
                                console.error('Error playing notification sound:', error);
                                // Try alternative method for browsers with autoplay restrictions
                                document.addEventListener('click', function playOnUserAction() {
                                    soundElement.play();
                                    document.removeEventListener('click', playOnUserAction);
                                }, { once: true });
                            });
                        }
                    } else {
                        console.error('Notification sound element not found');
                        // Fallback to creating a new audio instance with the custom sound
                        const fallbackAudio = new Audio('/sounds/noti.mp3');
                        fallbackAudio.play().catch(e => console.error('Fallback sound failed:', e));
                    }
                } catch (e) {
                    console.error('Error in playNotificationSound:', e);
                }
            };
            
            console.log('Enhanced notification sound system initialized successfully');
        } catch (e) {
            console.error('Error initializing notification sounds:', e);
        }
    }
    
    // Initialize the notification sounds
    initNotificationSounds();
    
    // Set a flag to indicate successful initialization
    window.notificationSoundSystemInitialized = true;
});