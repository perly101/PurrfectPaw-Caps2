<?php

// Create the sounds directory if it doesn't exist
$soundsDir = public_path('sounds');
if (!file_exists($soundsDir)) {
    mkdir($soundsDir, 0755, true);
}

// Check if the notification sound files exist
$notiPath = public_path('sounds/noti.mp3');
$notificationPath = public_path('sounds/notification.mp3');

// Check for JS files that handle notifications
$globalNotificationsJs = public_path('js/global-notifications.js');
$notificationsJs = public_path('js/notifications.js');

// Output results
$results = [
    'sound_directory_exists' => file_exists($soundsDir),
    'noti_exists' => file_exists($notiPath),
    'notification_exists' => file_exists($notificationPath),
    'noti_size' => file_exists($notiPath) ? filesize($notiPath) : 0,
    'notification_size' => file_exists($notificationPath) ? filesize($notificationPath) : 0,
    'js_files' => [
        'global-notifications.js' => [
            'exists' => file_exists($globalNotificationsJs),
            'size' => file_exists($globalNotificationsJs) ? filesize($globalNotificationsJs) : 0,
            'modified' => file_exists($globalNotificationsJs) ? date('Y-m-d H:i:s', filemtime($globalNotificationsJs)) : null
        ],
        'notifications.js' => [
            'exists' => file_exists($notificationsJs),
            'size' => file_exists($notificationsJs) ? filesize($notificationsJs) : 0,
            'modified' => file_exists($notificationsJs) ? date('Y-m-d H:i:s', filemtime($notificationsJs)) : null
        ]
    ],
    'public_js_files' => [],
    'troubleshooting_guide' => [
        'sound_issues' => [
            'check_files' => 'Ensure noti.mp3 exists in the public/sounds directory',
            'check_browser_settings' => 'Verify browser allows autoplay of audio',
            'check_user_settings' => 'Make sure sound is enabled in user settings (localStorage.notification_sound_enabled)',
            'try_manual_play' => 'Try manually playing the sound with a user interaction (click)'
        ],
        'notification_issues' => [
            'check_container' => 'Verify that #global-notification-container exists in the layout',
            'check_js_files' => 'Ensure global-notifications.js and notifications.js are loaded',
            'check_errors' => 'Look for JavaScript errors in the browser console',
            'test_page' => 'Use the test page at /test/global-notification-test to diagnose issues'
        ]
    ],
    'documentation' => 'See NOTIFICATION-SYSTEM-GUIDE.md for comprehensive documentation'
];

// Check JS files
$jsDir = public_path('js');
if (file_exists($jsDir)) {
    $jsFiles = glob($jsDir . '/*.js');
    foreach ($jsFiles as $file) {
        $basename = basename($file);
        $results['public_js_files'][] = [
            'file' => $basename,
            'size' => filesize($file),
            'modified' => date('Y-m-d H:i:s', filemtime($file)),
            'is_notification_related' => (strpos($basename, 'notification') !== false || strpos($basename, 'Notification') !== false)
        ];
    }
}

// HTML Output for better readability
if (isset($_GET['format']) && $_GET['format'] === 'html') {
    header('Content-Type: text/html');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Notification System Diagnostics</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 1000px;
                margin: 0 auto;
                padding: 20px;
            }
            h1 { color: #2563eb; }
            h2 { color: #4338ca; margin-top: 30px; }
            .success { color: #16a34a; }
            .warning { color: #d97706; }
            .error { color: #dc2626; }
            .card {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                border-bottom: 1px solid #e5e7eb;
                text-align: left;
            }
            th {
                background-color: #f9fafb;
            }
            .action-btn {
                display: inline-block;
                background-color: #2563eb;
                color: white;
                padding: 8px 16px;
                border-radius: 4px;
                text-decoration: none;
                margin-top: 10px;
            }
            pre {
                background-color: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                overflow-x: auto;
            }
            .test-sound-btn {
                padding: 8px 16px;
                background-color: #4f46e5;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <h1>Notification System Diagnostics</h1>
        
        <div class="card">
            <h2>Sound Files</h2>
            <table>
                <tr>
                    <th>File</th>
                    <th>Status</th>
                    <th>Size</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <td>sounds directory</td>
                    <td class="<?= $results['sound_directory_exists'] ? 'success' : 'error' ?>">
                        <?= $results['sound_directory_exists'] ? 'Exists' : 'Missing' ?>
                    </td>
                    <td>-</td>
                    <td>
                        <?php if (!$results['sound_directory_exists']) { ?>
                            <span class="warning">Run this page again to create directory</span>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>noti.mp3</td>
                    <td class="<?= $results['noti_exists'] ? 'success' : 'error' ?>">
                        <?= $results['noti_exists'] ? 'Exists' : 'Missing' ?>
                    </td>
                    <td><?= $results['noti_size'] ? round($results['noti_size'] / 1024, 2) . ' KB' : '-' ?></td>
                    <td>
                        <?php if ($results['noti_exists']) { ?>
                            <button class="test-sound-btn" onclick="playSound('/sounds/noti.mp3')">Test Sound</button>
                        <?php } else { ?>
                            <span class="error">Upload noti.mp3 to the sounds directory</span>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>notification.mp3</td>
                    <td class="<?= $results['notification_exists'] ? 'success' : 'warning' ?>">
                        <?= $results['notification_exists'] ? 'Exists' : 'Missing (optional)' ?>
                    </td>
                    <td><?= $results['notification_size'] ? round($results['notification_size'] / 1024, 2) . ' KB' : '-' ?></td>
                    <td>
                        <?php if ($results['notification_exists']) { ?>
                            <button class="test-sound-btn" onclick="playSound('/sounds/notification.mp3')">Test Sound</button>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="card">
            <h2>JavaScript Files</h2>
            <table>
                <tr>
                    <th>File</th>
                    <th>Status</th>
                    <th>Size</th>
                    <th>Last Modified</th>
                </tr>
                <tr>
                    <td>global-notifications.js</td>
                    <td class="<?= $results['js_files']['global-notifications.js']['exists'] ? 'success' : 'error' ?>">
                        <?= $results['js_files']['global-notifications.js']['exists'] ? 'Exists' : 'Missing' ?>
                    </td>
                    <td><?= $results['js_files']['global-notifications.js']['size'] ? round($results['js_files']['global-notifications.js']['size'] / 1024, 2) . ' KB' : '-' ?></td>
                    <td><?= $results['js_files']['global-notifications.js']['modified'] ?: '-' ?></td>
                </tr>
                <tr>
                    <td>notifications.js</td>
                    <td class="<?= $results['js_files']['notifications.js']['exists'] ? 'success' : 'warning' ?>">
                        <?= $results['js_files']['notifications.js']['exists'] ? 'Exists' : 'Missing' ?>
                    </td>
                    <td><?= $results['js_files']['notifications.js']['size'] ? round($results['js_files']['notifications.js']['size'] / 1024, 2) . ' KB' : '-' ?></td>
                    <td><?= $results['js_files']['notifications.js']['modified'] ?: '-' ?></td>
                </tr>
            </table>
        </div>
        
        <div class="card">
            <h2>Quick Tests</h2>
            <div>
                <button class="test-sound-btn" onclick="testGlobalNotification()">Test Global Notification</button>
                <button class="test-sound-btn" style="background-color: #059669;" onclick="testNotificationSound()">Test Sound Only</button>
            </div>
            <div style="margin-top: 20px;">
                <a href="/test/global-notification-test" class="action-btn">Open Notification Test Page</a>
            </div>
        </div>
        
        <div class="card">
            <h2>Troubleshooting Guide</h2>
            
            <h3>Sound Issues</h3>
            <ul>
                <?php foreach ($results['troubleshooting_guide']['sound_issues'] as $tip) { ?>
                    <li><?= $tip ?></li>
                <?php } ?>
            </ul>
            
            <h3>Notification Issues</h3>
            <ul>
                <?php foreach ($results['troubleshooting_guide']['notification_issues'] as $tip) { ?>
                    <li><?= $tip ?></li>
                <?php } ?>
            </ul>
            
            <h3>Documentation</h3>
            <p>For comprehensive documentation on the notification system, refer to:</p>
            <p><a href="/NOTIFICATION-SYSTEM-GUIDE.md" target="_blank">NOTIFICATION-SYSTEM-GUIDE.md</a></p>
        </div>
        
        <script>
            // Function to test playing sound
            function playSound(url) {
                const audio = new Audio(url);
                audio.volume = 1.0;
                
                audio.play().then(() => {
                    console.log('Sound played successfully');
                }).catch(error => {
                    console.error('Error playing sound:', error);
                    alert('Error playing sound: ' + error.message);
                });
            }
            
            // Function to test the global notification system
            function testGlobalNotification() {
                if (window.globalNotificationSystem && typeof showGlobalNotification === 'function') {
                    const testNotification = {
                        id: 'test-' + Date.now(),
                        type: 'system_test',
                        data: {
                            title: 'Diagnostic Test',
                            body: 'This is a test of the global notification system from the diagnostic page',
                            created_at: new Date().toISOString()
                        },
                        created_at: new Date().toISOString(),
                        read_at: null
                    };
                    
                    showGlobalNotification(testNotification);
                    console.log('Global notification test executed');
                } else {
                    alert('Global notification system not available! Make sure global-notifications.js is loaded on this page.');
                }
            }
            
            // Function to test notification sound
            function testNotificationSound() {
                if (typeof playGlobalNotificationSound === 'function') {
                    playGlobalNotificationSound();
                    console.log('Global notification sound played');
                } else {
                    // Fallback
                    playSound('/sounds/noti.mp3');
                }
            }
            
            // Check if the global notification system is loaded
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    if (window.globalNotificationSystem) {
                        console.log('Global notification system detected:', window.globalNotificationSystem);
                    } else {
                        console.warn('Global notification system not detected on this page');
                    }
                }, 500);
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// JSON Output (default)
header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);