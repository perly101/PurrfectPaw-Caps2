<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Subscription;

class ClinicRegistrationController extends Controller
{
    /**
     * Send verification email with OTP to the user - Using Gmail account
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
        $errorMsg = '';
        $logPrefix = "Email: ";
        
        // Try sending the email with Laravel's Mail and Gmail settings
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
            
            \Log::info("{$logPrefix}Email sent to {$email} using Laravel Mail with Gmail");
            $emailSent = true;
            return true;
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            \Log::warning("{$logPrefix}Primary mail method failed: {$errorMsg}. Trying fallback method.");
        }
        
        // Fallback 1: Try using Swift Mailer directly with Gmail settings from .env
        if (!$emailSent) {
            try {
                // Configure Swift Mailer with Gmail settings directly from .env
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
                
                $mailer = new \Swift_Mailer($transport);
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
        
        // Fallback 2: Direct PHP mail function as final fallback
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
            throw new \Exception("Could not send verification email. Please try again or contact support.");
        }
    }
    
    /**
     * Show step 1 of the clinic registration form
     */
    public function showStep1($plan = null)
    {
        // Set default plan to monthly if not provided
        if ($plan === null) {
            $plan = 'monthly';
        }
        
        // Validate that plan is either 'monthly' or 'yearly'
        if (!in_array($plan, ['monthly', 'yearly'])) {
            return redirect()->route('landing')->with('error', 'Invalid subscription plan selected.');
        }
        
        // Determine the subscription amount based on the plan
        $planDetails = [
            'plan_name' => $plan === 'monthly' ? 'Monthly Plan' : 'Annual Plan',
            'amount' => $plan === 'monthly' ? 10000 : 120000,
            'plan_type' => $plan,
            'billing_cycle' => $plan === 'monthly' ? 'monthly' : 'yearly',
        ];
        
        return view('clinic.register.step1', compact('planDetails'));
    }
    
    /**
     * Store step 1 data and redirect to step 2
     */
    public function storeStep1(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'plan_type' => 'required|string|in:monthly,yearly',
            'amount' => 'required|numeric',
            'billing_cycle' => 'required|string|in:monthly,yearly',
        ]);
        
        try {
            // Create a new array with the validated data without the file
            $clinicData = $request->except(['logo', '_token']);
            
            // Store data in session for later use
            $request->session()->put('clinic_step1', $clinicData);
            
            // Handle logo upload if provided
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoPath = $logo->store('clinic_logos', 'public');
                $request->session()->put('clinic_logo_path', $logoPath);
            }
            
            return redirect()->route('clinic.register.step2');
        } catch (\Exception $e) {
            \Log::error('Error in storeStep1: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred. Please try again.');
        }
    }
    
    /**
     * Show step 2 of the registration form
     */
    public function showStep2(Request $request)
    {
        // Check if step 1 was completed
        if (!$request->session()->has('clinic_step1')) {
            return redirect()->route('landing')
                ->with('error', 'Please complete step 1 of the registration process first.');
        }
        
        try {
            $clinicData = $request->session()->get('clinic_step1');
            
            // Make sure we have no unserializable objects in the data
            if (isset($clinicData['logo']) && is_object($clinicData['logo'])) {
                unset($clinicData['logo']);
                $request->session()->put('clinic_step1', $clinicData);
            }
            
            return view('clinic.register.step2', compact('clinicData'));
            
        } catch (\Exception $e) {
            // If there's an error, clear the session and redirect back to step 1
            $request->session()->forget(['clinic_step1', 'clinic_logo_path']);
            return redirect()->route('landing')
                ->with('error', 'There was an error with your registration data. Please try again.');
        }
    }
    
    /**
     * Process the final step of registration
     */
    public function storeStep2(Request $request)
    {
        // Validate the form data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:male,female,prefer_not_say',
            'birthday' => 'nullable|date',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Check if step 1 was completed
        if (!$request->session()->has('clinic_step1')) {
            return redirect()->route('landing')
                ->with('error', 'Please complete step 1 of the registration process first.');
        }
        
        try {
            // Start transaction
            DB::beginTransaction();
            
            // Get clinic data from session
            $clinicData = $request->session()->get('clinic_step1');
            
            // Debug log
            Log::info('Clinic data from session:', $clinicData);
            
            // Create user account for clinic staff
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'clinic',
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'phone_number' => $request->phone_number,
                'email_verified_at' => null, // Must be verified by the user
            ]);
            
            // Create clinic record with owner relationship
            $clinic = Clinic::create([
                'clinic_name' => $clinicData['name'],
                'address' => $clinicData['address'],
                'contact_number' => $clinicData['contact_number'],
                'profile_picture' => $request->session()->has('clinic_logo_path') 
                    ? $request->session()->get('clinic_logo_path') 
                    : null,
                'owner_id' => $user->id,
                'user_id' => $user->id, // Set user_id to connect staff account with clinic
                'status' => 'pending_verification', // Set initial status as pending email verification
            ]);
            
            // Update user with clinic association
            $user->clinic_id = $clinic->id;
            $user->save();
            
            // Create subscription record with proper error handling
            try {
                Log::info('Creating subscription with data:', [
                    'clinic_id' => $clinic->id,
                    'plan_type' => $clinicData['plan_type'],
                    'amount' => $clinicData['amount'],
                    'billing_cycle' => $clinicData['plan_type'] === 'monthly' ? 'monthly' : 'yearly',
                ]);
                
                $subscription = Subscription::create([
                    'clinic_id' => $clinic->id,
                    'plan_type' => $clinicData['plan_type'],
                    'amount' => $clinicData['amount'],
                    'billing_cycle' => $clinicData['plan_type'] === 'monthly' ? 'monthly' : 'yearly',
                    'status' => 'pending',
                    'start_date' => null, // Will be set after payment
                    'end_date' => null,   // Will be calculated after payment
                ]);
            } catch (\Exception $subscriptionError) {
                throw new \Exception("Error creating subscription: " . $subscriptionError->getMessage(), 0, $subscriptionError);
            }
            
            // Store subscription ID and user ID in session for the payment page
            $request->session()->put('subscription_id', $subscription->id);
            $request->session()->put('clinic_registration_user_id', $user->id);
            
            // Generate OTP for email verification
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Save OTP to database
            \App\Models\EmailVerificationOtp::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'otp' => $otp,
                'expires_at' => \Carbon\Carbon::now()->addMinutes(30),
            ]);
            
            // Commit transaction (commit before email sending to ensure database changes are saved)
            DB::commit();
            
            try {
                // Send verification email
                $this->sendVerificationEmail($user, $otp);
            } catch (\Exception $emailException) {
                // Log the error but don't throw it - we'll use the OTP from the database
                Log::error('Email sending failed but continuing: ' . $emailException->getMessage());
                
                // If we're in a local/development/testing environment, display the OTP in the session flash
                if (app()->environment('local', 'development', 'testing')) {
                    $request->session()->flash('dev_otp', $otp);
                }
            }
            
            // Clear the step data
            $request->session()->forget(['clinic_step1', 'clinic_logo_path']);
            
            return redirect()->route('clinic.register.verification');
            
        } catch (\Exception $e) {
            // Roll back transaction on error
            DB::rollBack();
            Log::error('Clinic registration error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred during registration. Please try again. (' . $e->getMessage() . ')');
        }
    }
}