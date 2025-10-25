<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiKey;
    protected $senderName;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('SMS_API_KEY', '6dff29a20c4ad21b0ff30725e15c23d0');
        $this->senderName = env('SMS_SENDER_NAME', 'AutoRepair');
        $this->baseUrl = 'https://semaphore.co/api/v4/messages';
    }

    /**
     * Send SMS message via Semaphore API
     *
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    public function sendSms($phoneNumber, $message)
    {
        // Check if SMS is enabled
        if (!env('SMS_ENABLED', true)) {
            Log::info('SMS disabled - would have sent message', [
                'phone' => $phoneNumber,
                'message' => $message
            ]);
            
            return [
                'success' => true,
                'message' => 'SMS disabled - message logged only',
                'data' => ['message_id' => 'DISABLED_' . time()]
            ];
        }
        
        try {
            // Format phone number to ensure it has country code
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for development
                'timeout' => 30
            ])->post($this->baseUrl, [
                'apikey' => $this->apiKey,
                'number' => $formattedPhone,
                'message' => $message,
                'sendername' => $this->senderName
            ]);

            $responseData = $response->json();

            // Log API response for debugging (remove in production if not needed)
            Log::debug('Semaphore API Response', [
                'status_code' => $response->status(),
                'response_body' => $responseData
            ]);

            if ($response->successful() && isset($responseData[0]['status']) && 
                in_array($responseData[0]['status'], ['Queued', 'Pending', 'Sent'])) {
                Log::info('SMS sent successfully', [
                    'phone' => $formattedPhone,
                    'message_id' => $responseData[0]['message_id'] ?? null,
                    'status' => $responseData[0]['status']
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $responseData[0]
                ];
            } else {
                Log::error('SMS sending failed', [
                    'phone' => $formattedPhone,
                    'response' => $responseData,
                    'status_code' => $response->status()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS',
                    'error' => $responseData['message'] ?? ($responseData[0]['message'] ?? 'Unknown error'),
                    'full_response' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS service error', [
                'phone' => $phoneNumber,
                'message' => $message,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'SMS service error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to Philippine format (+63)
     *
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If it starts with 0, replace with +63
        if (substr($phone, 0, 1) === '0') {
            $phone = '63' . substr($phone, 1);
        }
        
        // If it doesn't start with 63, add it
        if (substr($phone, 0, 2) !== '63') {
            $phone = '63' . $phone;
        }
        
        return $phone;
    }

    /**
     * Send appointment confirmation SMS
     *
     * @param string $phoneNumber
     * @param array $appointmentData
     * @return array
     */
    public function sendAppointmentConfirmation($phoneNumber, $appointmentData)
    {
        $message = $this->buildAppointmentConfirmationMessage($appointmentData);
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Build appointment confirmation message
     *
     * @param array $appointmentData
     * @return string
     */
    private function buildAppointmentConfirmationMessage($appointmentData)
    {
        $clinicName = $appointmentData['clinic_name'] ?? 'AutoRepair Clinic';
        $appointmentDate = $appointmentData['appointment_date'] ?? '';
        $appointmentTime = $appointmentData['appointment_time'] ?? '';
        $doctorName = $appointmentData['doctor_name'] ?? 'Available Doctor';
        $petName = $appointmentData['pet_name'] ?? 'your pet';

        $message = "Good day! Your appointment at {$clinicName} has been CONFIRMED.\n\n";
        $message .= "Details:\n";
        $message .= "Date: {$appointmentDate}\n";
        $message .= "Time: {$appointmentTime}\n";
        $message .= "Doctor: Dr. {$doctorName}\n";
        $message .= "Pet: {$petName}\n\n";
        $message .= "Please arrive on time to avoid complications. Thank you!";

        return $message;
    }

    /**
     * Send appointment reminder SMS
     *
     * @param string $phoneNumber
     * @param array $appointmentData
     * @return array
     */
    public function sendAppointmentReminder($phoneNumber, $appointmentData)
    {
        $clinicName = $appointmentData['clinic_name'] ?? 'AutoRepair Clinic';
        $appointmentDate = $appointmentData['appointment_date'] ?? '';
        $appointmentTime = $appointmentData['appointment_time'] ?? '';

        $message = "Reminder: You have an appointment at {$clinicName} tomorrow ({$appointmentDate}) at {$appointmentTime}. Please be on time. Thank you!";

        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send appointment cancellation SMS
     *
     * @param string $phoneNumber
     * @param array $appointmentData
     * @return array
     */
    public function sendAppointmentCancellation($phoneNumber, $appointmentData)
    {
        $clinicName = $appointmentData['clinic_name'] ?? 'AutoRepair Clinic';
        $appointmentDate = $appointmentData['appointment_date'] ?? '';
        $appointmentTime = $appointmentData['appointment_time'] ?? '';

        $message = "Your appointment at {$clinicName} on {$appointmentDate} at {$appointmentTime} has been CANCELLED. Please contact us to reschedule. Thank you.";

        return $this->sendSms($phoneNumber, $message);
    }
}
