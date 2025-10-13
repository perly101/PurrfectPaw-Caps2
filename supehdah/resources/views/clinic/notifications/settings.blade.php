@php
    use App\Models\ClinicInfo;
    $clinic = ClinicInfo::where('user_id', auth()->id())->first();
@endphp

<x-app-layout>
    {{-- Include mobile navigation (only visible on mobile) --}}
    @include('clinic.components.mobile-nav')

    <div class="py-6 md:py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:space-x-6">

            {{-- Sidebar (hidden on mobile) --}}
            <div class="hidden md:block md:w-1/4 lg:w-1/4">
                @include('clinic.components.sidebar')
            </div>

            {{-- Main Content --}}
            <div class="w-full md:w-3/4 mt-16 md:mt-0">
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="p-4 md:p-6 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">Notification Settings</h2>
                        </div>
                    </div>

                    <div class="p-4 md:p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Sound Settings</h3>
                            
                            <div class="flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <h4 class="text-md font-medium text-gray-700">Notification Sound</h4>
                                    <p class="text-sm text-gray-500 mt-1">Play a sound when new notifications are received</p>
                                </div>
                                <div class="flex items-center">
                                    <label class="inline-flex relative items-center cursor-pointer">
                                        <input type="checkbox" id="notification-sound-toggle" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <h4 class="text-md font-medium text-gray-700">Test UI Notification</h4>
                                    <p class="text-sm text-gray-500 mt-1">Test the notification sound and popup locally</p>
                                </div>
                                <button id="test-notification-btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                                    Test UI Notification
                                </button>
                            </div>
                            
                            <div class="mt-4 flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <h4 class="text-md font-medium text-gray-700">Generate Real Test Notification</h4>
                                    <p class="text-sm text-gray-500 mt-1">Create a real notification in the system for testing</p>
                                </div>
                                <a href="{{ route('clinic.notifications.test') }}" id="server-test-btn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                                    Create Real Notification
                                </a>
                            </div>
                        </div>
                        
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Display Settings</h3>
                            
                            <div class="flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <h4 class="text-md font-medium text-gray-700">Popup Notifications</h4>
                                    <p class="text-sm text-gray-500 mt-1">Show popup notifications when new notifications arrive</p>
                                </div>
                                <div class="flex items-center">
                                    <label class="inline-flex relative items-center cursor-pointer">
                                        <input type="checkbox" id="popup-notification-toggle" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Save settings in localStorage
            const soundToggle = document.getElementById('notification-sound-toggle');
            const popupToggle = document.getElementById('popup-notification-toggle');
            const testBtn = document.getElementById('test-notification-btn');
            
            // Load saved settings
            soundToggle.checked = localStorage.getItem('notification_sound_enabled') !== 'false';
            popupToggle.checked = localStorage.getItem('notification_popup_enabled') !== 'false';
            
            // Save settings on change
            soundToggle.addEventListener('change', function() {
                localStorage.setItem('notification_sound_enabled', soundToggle.checked);
            });
            
            popupToggle.addEventListener('change', function() {
                localStorage.setItem('notification_popup_enabled', popupToggle.checked);
            });
            
            // Test notification
            testBtn.addEventListener('click', function() {
                // Create a mock notification
                const mockNotification = {
                    type: 'clinic_new_appointment',
                    data: {
                        title: 'Test Notification',
                        body: 'This is a test notification to check your notification settings.',
                        appointment_id: null
                    }
                };
                
                // Show notification popup if enabled
                if (popupToggle.checked) {
                    if (typeof showNotificationPopup === 'function') {
                        showNotificationPopup(mockNotification);
                    }
                }
                
                // Play sound if enabled
                if (soundToggle.checked) {
                    if (typeof playNotificationSound === 'function') {
                        playNotificationSound();
                    }
                }
                
                // Show success message
                Swal.fire({
                    title: 'Test Notification Sent',
                    text: 'The test notification was triggered successfully.',
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        });
    </script>
</x-app-layout>