<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationOtp;
use App\Services\SwiftMailerFix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class OtpVerificationController extends Controller
{
    /**
     * Generate and send OTP to a specific user.
     * Can be called programmatically without a request.
     *
     * @param  \App\Models\User  $user
     * @return bool Success status
     */
    public function generateAndSendOtp($user)
    {
        try {
            // Generate new OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Delete any existing OTP for this user
            EmailVerificationOtp::where('user_id', $user->id)->delete();
            
            // Save new OTP to database
            EmailVerificationOtp::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(30),
            ]);
            
            // Send OTP via email
            $name = $user->first_name . ' ' . $user->last_name;
            $email = $user->email;
            
            // Always log the OTP in local environment for debugging
            if (app()->environment('local')) {
                \Log::info("Development mode: OTP for {$email} is: {$otp}");
            }
            
            // Send using SwiftMailerFix
            $success = SwiftMailerFix::sendMail(
                $email,
                $name,
                'Email Verification OTP',
                'emails.otp-verification',
                ['user' => $user, 'otp' => $otp]
            );
            
            return $success;
            
        } catch (\Exception $e) {
            \Log::error("OTP generation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify the OTP code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'min:6', 'max:6'],
        ]);

        $user = Auth::user();
        
        // Get the latest OTP record for this user
        $otpRecord = EmailVerificationOtp::where('user_id', $user->id)
                                        ->where('email', $user->email)
                                        ->first();
        
        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No verification code found.'
            ], 404);
        }
        
        if (Carbon::now()->isAfter($otpRecord->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired.'
            ], 400);
        }
        
        if ($otpRecord->otp !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.'
            ], 400);
        }
        
        // Mark email as verified
        $user->email_verified_at = Carbon::now();
        $user->save();
        
        // Delete the OTP record as it's been used
        $otpRecord->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.',
            'user' => $user
        ]);
    }

    /**
     * Resend the OTP verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Use the generateAndSendOtp method to handle OTP creation and sending
            $success = $this->generateAndSendOtp($user);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Verification code sent successfully.',
                    'status' => 'verification-link-sent'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again later.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error("API OTP resend error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not resend verification code. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check if the user's email is verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkVerified(Request $request)
    {
        $user = Auth::user();
        
        return response()->json([
            'verified' => $user->hasVerifiedEmail(),
            'email' => $user->email
        ]);
    }
}