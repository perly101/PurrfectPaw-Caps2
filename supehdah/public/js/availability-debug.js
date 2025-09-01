/**
 * Debug script for availability forms
 */
document.addEventListener('DOMContentLoaded', function() {
    // Day form submission debugging
    const dayForm = document.getElementById('dayScheduleForm');
    if (dayForm) {
        dayForm.addEventListener('submit', function(e) {
            // Log form data being submitted
            console.log('Day Schedule Form - Submit Event');
            const formData = new FormData(dayForm);
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Check specific elements
            const isClosed = document.getElementById('modalClosedInput');
            if (isClosed) {
                console.log('Is Closed Checkbox:', {
                    checked: isClosed.checked,
                    value: isClosed.value,
                    name: isClosed.name
                });
            }
        });
    }
    
    // Special date form submission debugging
    const specialDateForm = document.getElementById('specialDateForm');
    if (specialDateForm) {
        const submitBtn = specialDateForm.querySelector('button[type="button"]');
        if (submitBtn) {
            submitBtn.addEventListener('click', function() {
                console.log('Special date form submission attempted');
                console.log('CSRF token at submit time:', window.csrfToken);
            });
        }
    }
});
