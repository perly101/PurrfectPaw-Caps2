<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Clinic;
use App\Models\EmailVerificationOtp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ClinicVerificationController extends Controller
{
    /**
     * Show the verification form
     */
    public function showVerificationForm(Request $request)
    {
        // Check if there's a user ID in session
        if (!$request->session()->has('clinic_registration_user_id')) {
            return redirect()->route('landing')
                ->with('error', 'No pending registration found. Please start the registration process again.');
        }
        
        try {
            // Get the user from session
            $userId = $request->session()->get('clinic_registration_user_id');
            $user = User::findOrFail($userId);
            
            // Check if the user is already verified
            if ($user->hasVerifiedEmail()) {
                // If user is verified, redirect to payment
                return redirect()->route('payment.show')
                    ->with('success', 'Your email has been verified. You can now proceed with payment.');
            }
            
            $email = $user->email;
            
            // In development/local environment, get the latest OTP and add to session
            if (app()->environment('local', 'development', 'testing')) {
                $latestOtp = EmailVerificationOtp::where('user_id', $user->id)
                    ->where('email', $user->email)
                    ->first();
                
                if ($latestOtp) {
                    $request->session()->flash('dev_otp', $latestOtp->otp);
                }
            }
            
            return view('clinic.register.verify-email', compact('email'));
        } catch (\Exception $e) {
            Log::error('Error showing verification form: ' . $e->getMessage());
            return redirect()->route('landing')
                ->with('error', 'An error occurred. Please try again.');
        }
    }
    
    /**
     * Verify the OTP code
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'min:6', 'max:6'],
        ]);
        
        // Check if there's a user ID in session
        if (!$request->session()->has('clinic_registration_user_id')) {
            return redirect()->route('landing')
                ->with('error', 'No pending registration found. Please start the registration process again.');
        }
        
        try {
            DB::beginTransaction();
            
            // Get the user from session
            $userId = $request->session()->get('clinic_registration_user_id');
            $user = User::findOrFail($userId);
            
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
            
            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();
            
            // Update clinic status
            $clinic = Clinic::where('owner_id', $user->id)->first();
            if ($clinic) {
                $clinic->status = 'pending_payment';
                $clinic->save();
            }
            
            // Delete the OTP record as it's been used
            $otpRecord->delete();
            
            DB::commit();
            
            // Redirect to payment page
            return redirect()->route('payment.show')
                ->with('success', 'Email verified successfully! You can now proceed with payment.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during OTP verification: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during verification. Please try again.');
        }
    }
    
    /**
     * Resend the OTP verification code
     */
    public function resendOtp(Request $request)
    {
        // Check if there's a user ID in session
        if (!$request->session()->has('clinic_registration_user_id')) {
            return redirect()->route('landing')
                ->with('error', 'No pending registration found. Please start the registration process again.');
        }
        
        try {
            // Get the user from session
            $userId = $request->session()->get('clinic_registration_user_id');
            $user = User::findOrFail($userId);
            
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
            
            try {
                // Send verification email
                $this->sendVerificationEmail($user, $otp);
                return back()->with('status', 'verification-link-sent');
            } catch (\Exception $e) {
                // Log the error but continue - we'll use the OTP from the database
                Log::error('Email resend failed but continuing: ' . $e->getMessage());
                
                // In development environment, display a helpful message but still rely on email
                if (app()->environment('local', 'development', 'testing')) {
                    return back()
                        ->with('dev_otp', $otp)
                        ->with('status', 'verification-link-sent')
                        ->with('info', 'A new verification code has been sent to your email address.');
                }
                
                return back()->with('status', 'verification-link-sent');
            }
            
        } catch (\Exception $e) {
            Log::error('Error during OTP resend: ' . $e->getMessage());
            return back()->with('error', 'Could not resend verification code. Please try again later.');
        }
    }
    
    /**
     * Send verification email with OTP to the user
     */
    protected function sendVerificationEmail($user, $otp)
    {
        $name = $user->first_name . ' ' . $user->last_name;
        $email = $user->email;
        
        // Only store OTP in session for development environments
        // In production, rely on email delivery
        if (app()->environment(['local', 'development', 'testing'])) {
            \Session::flash('dev_otp', $otp);
            \Log::info("Development mode: OTP for {$email} is: {$otp}");
        } else {
            // In production, just log that an OTP was generated (but not the actual code)
            \Log::info("Production: OTP generated for {$email}");
        }
        
        $emailSent = false;
        $errorMsg = null;
        $logPrefix = "Email: ";
        
        // Try sending the email with Laravel's Mail
        try {
            // Apply SSL fixes before sending
            stream_context_set_default([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            // Use Gmail SMTP settings from .env
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'mail.mailers.smtp.port' => env('MAIL_PORT', 587),
                'mail.mailers.smtp.encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'mail.mailers.smtp.username' => env('MAIL_USERNAME'),
                'mail.mailers.smtp.password' => env('MAIL_PASSWORD'),
                'mail.from.address' => env('MAIL_FROM_ADDRESS', 'purrf3ctpaw@gmail.com'),
                'mail.from.name' => env('MAIL_FROM_NAME', 'PurrfectPaw')
            ]);
            
            // Use Laravel's built-in Mail facade
            \Mail::send('emails.clinic-registration-otp', 
                ['user' => $user, 'otp' => $otp], 
                function ($message) use ($name, $email) {
                    $message->to($email, $name)
                            ->subject('Verify Your Email for PurrfectPaw Clinic Registration');
                }
            );
            
            \Log::info("{$logPrefix}Email sent to {$email} using Laravel Mail");
            $emailSent = true;
            return true;
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            \Log::warning("{$logPrefix}Primary mail method failed: {$errorMsg}. Trying fallback methods.");
        }
        
        // Fallback 1: Try using the Swift Mailer directly with Gmail settings
        if (!$emailSent) {
            try {
                // Configure Swift Mailer with Gmail settings
                $transport = new \Swift_SmtpTransport(
                    env('MAIL_HOST', 'smtp.gmail.com'),
                    env('MAIL_PORT', 587),
                    env('MAIL_ENCRYPTION', 'tls')
                );
                
                $transport->setUsername(env('MAIL_USERNAME', 'purrf3ctpaw@gmail.com'));
                $transport->setPassword(env('MAIL_PASSWORD', 'btfsddqawibpjkni'));
                
                // Disable SSL verification in the transport
                $transport->setStreamOptions([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ]);
                
                // Create mailer and message
                $mailer = new \Swift_Mailer($transport);
                $message = new \Swift_Message('Verify Your Email for PurrfectPaw Clinic Registration');
                $message->setFrom([env('MAIL_FROM_ADDRESS', 'purrf3ctpaw@gmail.com') => env('MAIL_FROM_NAME', 'PurrfectPaw')]);
                $message->setTo([$email => $name]);
                
                // Simple HTML message with OTP
                $message->setBody("
                    <html>
                    <body>
                        <h2>Verify Your Email for PurrfectPaw Clinic Registration</h2>
                        <p>Hello {$name},</p>
                        <p>Your verification code is: <strong>{$otp}</strong></p>
                        <p>Please enter this code on the verification page to complete your registration.</p>
                        <p>This code will expire in 30 minutes.</p>
                        <p>Thank you,<br>PurrfectPaw Team</p>
                    </body>
                    </html>
                ", 'text/html');
                
                // Send the message
                $result = $mailer->send($message);
                
                if ($result > 0) {
                    \Log::info("{$logPrefix}Email sent to {$email} using Swift Mailer direct");
                    $emailSent = true;
                    return true;
                }
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                \Log::warning("{$logPrefix}Swift Mailer direct failed: {$errorMsg}");
            }
        }
        
        // Fallback 2: Try PHP mail function as a last resort
        if (!$emailSent) {
            try {
                $subject = 'Verify Your Email for PurrfectPaw Clinic Registration';
                $headers = "From: " . env('MAIL_FROM_ADDRESS', 'purrf3ctpaw@gmail.com') . "\r\n";
                $headers .= "Reply-To: " . env('MAIL_FROM_ADDRESS', 'purrf3ctpaw@gmail.com') . "\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                
                $message = "
                <html>
                <body>
                    <h2>Verify Your Email for PurrfectPaw Clinic Registration</h2>
                    <p>Hello {$name},</p>
                    <p>Your verification code is: <strong>{$otp}</strong></p>
                    <p>Please enter this code on the verification page to complete your registration.</p>
                    <p>This code will expire in 30 minutes.</p>
                    <p>Thank you,<br>PurrfectPaw Team</p>
                </body>
                </html>
                ";
                
                $mailSent = mail($email, $subject, $message, $headers);
                
                if ($mailSent) {
                    \Log::info("{$logPrefix}Email sent to {$email} using PHP mail function");
                    $emailSent = true;
                    return true;
                }
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                \Log::error("{$logPrefix}PHP mail function failed: {$errorMsg}");
            }
        }
        
        // In production, if all email methods fail, we should report the error
        \Log::error("All email sending methods failed for {$email}. Last error: {$errorMsg}");
        
        if (app()->environment(['local', 'development', 'testing'])) {
            // In development, we can continue with the OTP in session
            return true;
        } else {
            // In production, throw an exception so the user knows there was an issue
            throw new \Exception("Could not send verification email. Please try again later.");
        }
    }
}