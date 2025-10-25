/**
 * Subscription Status Checker
 * 
 * A standalone JavaScript module to check subscription status via AJAX
 */

class SubscriptionStatusChecker {
    constructor(checkUrl, refreshInterval = 30) {
        this.checkUrl = checkUrl;
        this.refreshInterval = refreshInterval; // in seconds
        this.countdownElement = document.getElementById('refresh-countdown');
        this.statusUpdateElement = document.getElementById('status-update');
        this.countdown = this.refreshInterval;
        this.countdownInterval = null;
    }

    /**
     * Initialize the checker
     */
    init() {
        // Check immediately
        this.checkStatus();
        
        // Set up the countdown
        this.startCountdown();
    }

    /**
     * Start the countdown timer
     */
    startCountdown() {
        // Clear any existing interval
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
        
        // Reset the countdown
        this.countdown = this.refreshInterval;
        if (this.countdownElement) {
            this.countdownElement.textContent = this.countdown;
        }
        
        // Update the countdown every second
        this.countdownInterval = setInterval(() => {
            this.countdown--;
            
            if (this.countdownElement) {
                this.countdownElement.textContent = this.countdown;
            }
            
            // When countdown reaches zero, check status and reset countdown
            if (this.countdown <= 0) {
                this.checkStatus();
                this.countdown = this.refreshInterval;
            }
        }, 1000);
    }

    /**
     * Check the subscription status via AJAX
     */
    checkStatus() {
        fetch(this.checkUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Status check response:', data);
            
            // If subscription is active, redirect to thank you page
            if (data.status === 'active') {
                if (this.statusUpdateElement) {
                    this.statusUpdateElement.textContent = 'Your subscription is now active! Redirecting...';
                    this.statusUpdateElement.classList.remove('hidden', 'text-yellow-600');
                    this.statusUpdateElement.classList.add('text-green-600', 'font-medium');
                }
                
                // Stop the countdown
                if (this.countdownInterval) {
                    clearInterval(this.countdownInterval);
                }
                
                // Redirect after showing message for 2 seconds
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error checking status:', error);
        });
    }
}

// Automatically initialize if we're on the thank-you-pending page
document.addEventListener('DOMContentLoaded', function() {
    const checkStatusElement = document.getElementById('refresh-countdown');
    if (checkStatusElement) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const checkUrl = document.getElementById('status-checker')?.getAttribute('data-check-url');
        
        if (checkUrl) {
            const checker = new SubscriptionStatusChecker(checkUrl);
            checker.init();
        }
    }
});