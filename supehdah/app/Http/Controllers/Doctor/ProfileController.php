<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->with('user')->firstOrFail();
        
        return view('doctor.profile.index', compact('doctor'));
    }
    
    /**
     * Update the doctor's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone_number' => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|max:50',
            'experience_years' => 'required|integer|min:0',
            'bio' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Update doctor profile
        $doctor->first_name = $request->first_name;
        $doctor->middle_name = $request->middle_name;
        $doctor->last_name = $request->last_name;
        $doctor->email = $request->email;
        $doctor->phone_number = $request->phone_number;
        $doctor->specialization = $request->specialization;
        $doctor->license_number = $request->license_number;
        $doctor->experience_years = $request->experience_years;
        $doctor->bio = $request->bio;
        
        // Handle photo upload if provided
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($doctor->photo && Storage::exists('public/doctor_photos/' . $doctor->photo)) {
                Storage::delete('public/doctor_photos/' . $doctor->photo);
            }
            
            // Store new photo
            $photoPath = $request->file('photo')->store('public/doctor_photos');
            $doctor->photo = basename($photoPath);
        }
        
        $doctor->save();
        
        // Update associated user email
        $user = User::find(Auth::id());
        $user->email = $request->email;
        $user->save();
        
        return redirect()->route('doctor.profile.index')
            ->with('success', 'Profile updated successfully');
    }
    
    /**
     * Update the doctor's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::find(Auth::id());
        
        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect',
            ]);
        }
        
        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        
        return redirect()->route('doctor.profile.index')
            ->with('success', 'Password updated successfully');
    }
}
