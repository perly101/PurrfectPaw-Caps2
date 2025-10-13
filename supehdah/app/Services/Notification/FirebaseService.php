<?php

namespace App\Services\Notification;

use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    /**
     * FCM API URL
     */
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    
    /**
     * FCM Server Key
     * Note: This should be stored in .env in a real application
     */
    protected $serverKey;
    
    public function __construct()
    {
        // Get the server key from .env
        $this->serverKey = env('FCM_SERVER_KEY', '');
    }
    
    /**
     * Send push notification via FCM
     *
     * @param array|string $deviceTokens One or more device tokens to send to
     * @param array $data Notification data
     * @return bool
     */
    public function sendPushNotification($deviceTokens, array $data)
    {
        try {
            if (empty($this->serverKey)) {
                Log::warning('FCM_SERVER_KEY not set in environment variables');
                return false;
            }
            
            // Prepare notification payload
            $payload = [
                'notification' => [
                    'title' => $data['title'] ?? 'New Notification',
                    'body' => $data['message'] ?? '',
                    'sound' => 'default',
                    'badge' => '1',
                ],
                'data' => $data,
                'priority' => 'high'
            ];
            
            // Set target (single token or multiple tokens)
            if (is_array($deviceTokens)) {
                $payload['registration_ids'] = $deviceTokens;
            } else {
                $payload['to'] = $deviceTokens;
            }
            
            // Send request to FCM
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $payload);
            
            // Log response
            if ($response->successful()) {
                Log::info('FCM notification sent successfully', [
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Failed to send FCM notification', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
            
        } catch (Exception $e) {
            Log::error('Error sending FCM notification', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}