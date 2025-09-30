<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerificationOtp;
use App\Providers\RouteServiceProvider;
use App\Services\MailService;
use App\Services\SwiftMailerFix;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:female,male,prefer_not_say'],
            'birthday' => ['nullable', 'date'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role
        ]);

        event(new Registered($user));

        try {
            // Generate OTP code
            $otp = $this->generateOtp();
            
            // Delete any existing OTP for this user
            EmailVerificationOtp::where('user_id', $user->id)->delete();
            
            // Save OTP to database
            EmailVerificationOtp::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(30), // OTP expires in 30 minutes
            ]);
    
            // Send OTP via email (will handle its own exceptions)
            $this->sendOtpEmail($user, $otp);
        } catch (\Exception $e) {
            // Log the error
            \Log::error("Error during registration OTP process: " . $e->getMessage());
            
            // We continue with login regardless of email success
            // The user can request a new OTP if needed
        }

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
    
    /**
     * Generate a random 6-digit OTP code.
     *
     * @return string
     */
    private function generateOtp()
    {
        // Generate a 6-digit random number
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Send OTP verification email to the user.
     *
     * @param  \App\Models\User  $user
     * @param  string  $otp
     * @return bool True if email was sent successfully, false otherwise
     */
    private function sendOtpEmail($user, $otp)
    {
        $name = $user->first_name . ' ' . $user->last_name;
        $email = $user->email;
        
        // Always log the OTP in local environment for debugging
        if (app()->environment('local')) {
            \Log::info("Development mode: OTP for {$email} is: {$otp}");
        }
        
        try {
            // Use our new SwiftMailerFix service (designed specifically for SSL certificate issues)
            $success = SwiftMailerFix::sendMail(
                $email,
                $name,
                'Email Verification OTP',
                'emails.otp-verification',
                ['user' => $user, 'otp' => $otp]
            );
            
            if ($success) {
                \Log::info("Verification email sent to {$email} using SwiftMailerFix");
                return true;
            }
            
            // If that fails, try with our MailService
            \Log::warning("SwiftMailerFix failed, trying MailService for {$email}");
            
            $success = MailService::sendMail(
                $email,
                $name,
                'Email Verification OTP',
                'emails.otp-verification',
                ['user' => $user, 'otp' => $otp]
            );
            
            if ($success) {
                \Log::info("Verification email sent to {$email} using MailService");
                return true;
            }
            
            // If MailService fails too, use Laravel's Mail facade as last resort
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
            
            Mail::send('emails.otp-verification', ['user' => $user, 'otp' => $otp], function ($message) use ($name, $email) {
                $message->to($email, $name)
                        ->subject('Email Verification OTP');
            });
            
            \Log::info("Verification email sent to {$email} using Laravel Mail facade");
            return true;
            
        } catch (\Exception $e) {
            // Log the error but don't break the flow
            \Log::error("Failed to send OTP email to {$email}: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            // Always log the OTP in development environment regardless of email success
            if (app()->environment('local')) {
                \Log::info("Email failed but OTP for {$email} is: {$otp}");
            }
            
            // Return false but don't throw exception to allow registration to continue
            return false;
        }
    }
}
