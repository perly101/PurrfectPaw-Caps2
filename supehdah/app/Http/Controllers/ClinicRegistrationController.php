<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Subscription;

class ClinicRegistrationController extends Controller
{
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
                'email_verified_at' => now(), // Auto verify for demo purposes
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
                'status' => 'pending_payment', // Set initial status as pending payment
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
            
            // Store subscription ID in session for the payment page
            $request->session()->put('subscription_id', $subscription->id);
            
            // Commit transaction
            DB::commit();
            
            // Clear the step data
            $request->session()->forget(['clinic_step1', 'clinic_logo_path']);
            
            return redirect()->route('payment.show');
            
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