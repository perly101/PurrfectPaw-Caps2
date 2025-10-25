<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\EmailVerificationOtp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestOtpEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:otp {email? : The email to send the test OTP to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test OTP email sending functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            $email = $this->ask('Enter email address to send test OTP');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }
        
        $this->info("Testing OTP email sending to: {$email}");
        
        // Generate an OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->info("Generated OTP: {$otp}");
        
        // Create a test user if needed
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->info("Creating test user for {$email}");
            $user = new User();
            $user->name = 'Test User';
            $user->email = $email;
            $user->first_name = 'Test';
            $user->last_name = 'User';
            $user->password = bcrypt('password');
            $user->role = 'test';
            $user->save();
        } else {
            $this->info("Using existing user: {$user->name}");
        }
        
        // Store OTP in database
        EmailVerificationOtp::updateOrCreate(
            ['user_id' => $user->id, 'email' => $email],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(30),
            ]
        );
        
        $this->info("OTP stored in database");
        
        // Try sending with Laravel Mail
        $this->info("Attempting to send email with Laravel Mail...");
        
        try {
            // Apply SSL fixes before sending
            stream_context_set_default([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            // Configure mail settings
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'mail.mailers.smtp.port' => env('MAIL_PORT', 587),
                'mail.mailers.smtp.encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'mail.mailers.smtp.username' => env('MAIL_USERNAME'),
                'mail.mailers.smtp.password' => env('MAIL_PASSWORD'),
                'mail.from.address' => env('MAIL_FROM_ADDRESS'),
                'mail.from.name' => env('MAIL_FROM_NAME')
            ]);
            
            Mail::send('emails.clinic-registration-otp', 
                ['user' => $user, 'otp' => $otp], 
                function ($message) use ($email, $user) {
                    $message->to($email, $user->name)
                            ->subject('Test OTP for PurrfectPaw Clinic');
                }
            );
            
            $this->info("Email sent successfully!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Email sending failed: " . $e->getMessage());
            Log::error("Email sending failed in test command: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 1;
        }
    }
}