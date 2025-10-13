// This JavaScript file creates a notification sound file using a data URL
// Add this to your public/js folder and include it in your layout file
document.addEventListener('DOMContentLoaded', function() {
    // Function to create a notification sound file from data URL
    function createNotificationSoundFile() {
        // Data URL for a short notification sound
        const soundData = 'data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA/+M4wAAAAAAAAAAAAFhpbmcAAAAPAAAAAwAAA3YAlpaWlpaWlpaWlpaWlpaWlpaWlpaWlpaWlpaWlpaWlpaW8PDw8PDw8PDw8PDw8PDw8PDw8PDw8PDw8PDw8PDw8PDw////////////////////////////////////////////AAAAAExhdmM1OC4xMwAAAAAAAAAAAAAAACQCkAAAAAAAAANoPsKyjHQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/zWMQAFaCVs0UcAAFshTu5VygALSC9CSY5mZXKuJkR3MzM5YCFQDFma2b0wAHv8R/+L/MBNtXKaBg1GrEJIkkJIkxMpIQzJ1f/ARZUBL5VXFf/8DAAHL/8w1Y/Q8oU//z2MQBAYSMpyVxgAQyIqOp7RqAOafUCmbTrQNLsYg48X3iGpBMozs61KDBgYMGKnGFDGKMjK1alaiEBXKrCqIkTnVjGCRETDaIhjX//+zs7OiD3/+fEI0XR+tH1o1///7vdEbDI3Oi8bKv/81jEAQI8jJ4ysKACQ5K5NMYycAr6C6J5huCEMEArqhCk3rUIw6UtjtTqsJEXyHNnazW7VaGaVrVdHx8f3av/69nZ3Fs7OzvZ2REdWKFCIrV//3vbrEhY+Qsb9TYiI8Z1IeUqEP/zWMQCATSN5Cx6FABJ0Tu5WEsAKfLq62Nio4JC40MKB4iJjg8hXwoSGC5AcHlCpIRIU///9bm/ll/6f////+s9Z6iwxQfUG5KSFp6y0RWCVWWVVWJCTR6y008Fd9IxJpImhGlUf/NY5AGCcI3ULPIABDsjvKseEABWJIZE0TRKJJAEiSQAnj84MBgMf//nx8eHpw4H//8fHRkcP////x5iggKChUWggBgAAUCAHBgUFAAAgCQAAv4MABQAGCgwEAgBEQIAYDBgUFAgF//NYxA+SSIXarHmACAAIAq1naCgkVMhoIizC4REVERERCIu8u7u7REVERERCIi7y7y7RERERERERFRVVRERERXu7oiLu7u7u7u7u7u7u7u7u7u7v/+8u9u7u7u7u8RERERERCIu7u7u/+M4wD/AAAGkAAAAACIu7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u4=';
        
        // Function to convert data URL to Blob
        function dataURLtoBlob(dataURL) {
            const parts = dataURL.split(';base64,');
            const contentType = parts[0].split(':')[1];
            const raw = window.atob(parts[1]);
            const rawLength = raw.length;
            const uInt8Array = new Uint8Array(rawLength);
            
            for (let i = 0; i < rawLength; ++i) {
                uInt8Array[i] = raw.charCodeAt(i);
            }
            
            return new Blob([uInt8Array], { type: contentType });
        }
        
        // Create the blob and save it
        const blob = dataURLtoBlob(soundData);
        const objectURL = URL.createObjectURL(blob);
        
        // Create an audio element
        const audio = new Audio();
        audio.id = 'notification-sound';
        audio.src = objectURL;
        audio.style.display = 'none';
        document.body.appendChild(audio);
        
        // Expose a global function to play the sound
        window.playNotificationSound = function() {
            const soundElement = document.getElementById('notification-sound');
            if (soundElement) {
                soundElement.play().catch(e => console.log('Could not play notification sound', e));
            }
        };
        
        console.log('Notification sound initialized');
    }
    
    // Initialize notification sound
    createNotificationSoundFile();
});