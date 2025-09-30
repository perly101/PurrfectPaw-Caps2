<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerificationOtp;
use App\Providers\RouteServiceProvider;
use App\Services\MailService;
use App\Services\SwiftMailerFix;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class OtpVerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the OTP verification form.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.verify-otp');
    }

    /**
     * Verify the OTP code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
            return back()->withErrors(['otp' => 'No verification code found.']);
        }
        
        if (Carbon::now()->isAfter($otpRecord->expires_at)) {
            return back()->withErrors(['otp' => 'Verification code has expired.']);
        }
        
        if ($otpRecord->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }
        
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        
        // Delete the OTP record as it's been used
        $otpRecord->delete();
        
        return redirect(RouteServiceProvider::HOME)->with('verified', true);
    }

    /**
     * Resend the OTP verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        try {
            $user = Auth::user();
            
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
            
            $emailSent = false;
            $errorMsg = null;
            
            // 1. Try with our SwiftMailerFix service first (designed specifically for SSL certificate issues)
            try {
                $success = SwiftMailerFix::sendMail(
                    $email,
                    $name,
                    'Email Verification OTP',
                    'emails.otp-verification',
                    ['user' => $user, 'otp' => $otp]
                );
                
                if ($success) {
                    \Log::info("Verification email resent to {$email} using SwiftMailerFix");
                    $emailSent = true;
                }
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                \Log::error("SwiftMailerFix failed: " . $errorMsg);
            }
            
            // 2. If SwiftMailerFix fails, try with our MailService
            if (!$emailSent) {
                try {
                    \Log::warning("SwiftMailerFix failed, trying MailService for {$email}");
                    
                    $success = MailService::sendMail(
                        $email,
                        $name,
                        'Email Verification OTP',
                        'emails.otp-verification',
                        ['user' => $user, 'otp' => $otp]
                    );
                    
                    if ($success) {
                        \Log::info("Verification email resent to {$email} using MailService");
                        $emailSent = true;
                    }
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    \Log::error("MailService failed: " . $errorMsg);
                }
            }
            
            // 3. If all else fails, use Laravel's Mail facade as last resort
            if (!$emailSent) {
                try {
                    \Log::warning("All direct methods failed, using Laravel Mail facade for {$email}");
                    
                    // Apply global SSL fixes before trying Laravel Mail
                    stream_context_set_default([
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                            'security_level' => 0,
                            'verify_depth' => 0,
                            'disable_compression' => true
                        ]
                    ]);
                    
                    \Mail::send('emails.otp-verification', ['user' => $user, 'otp' => $otp], function ($message) use ($name, $email) {
                        $message->to($email, $name)
                                ->subject('Email Verification OTP');
                    });
                    
                    \Log::info("Verification email resent to {$email} using Laravel Mail facade");
                    $emailSent = true;
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    \Log::error("Laravel Mail facade failed: " . $errorMsg);
                }
            }
            
            if ($emailSent) {
                return back()->with('status', 'verification-link-sent');
            } else {
                \Log::error("All email sending methods failed for {$email}. Last error: {$errorMsg}");
                return back()->with('error', 'Could not send verification code. Please try again later.');
            }
            
        } catch (\Exception $e) {
            \Log::error("Error during OTP resend process: " . $e->getMessage());
            return back()->with('error', 'Could not resend verification code. Please try again later.');
        }
    }
}
