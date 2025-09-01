<?php

// app/Http/Controllers/Step2Controller.php

namespace App\Http\Controllers;

use App\Models\ClinicInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Step2Controller extends Controller
{
    public function create()
    {
        // Ensure clinic_info is set
        if (!session()->has('clinic_info')) {
            return redirect()->route('step1.create')->withErrors(['message' => 'Please complete step 1 first.']);
        }

        return view('admin.step2');
    }

public function store(Request $request)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'phone_number' => 'nullable|string|max:20',
        'gender' => 'nullable|in:female,male,prefer_not_say',
        'birthday' => 'nullable|date',
        'password' => 'required|string|confirmed|min:6',
    ]);

    $clinicData = session('clinic_info');

    // Create user with clinic role (not logging them in)
    $user = User::create([
        'first_name' => $request->first_name,
        'middle_name' => $request->middle_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'gender' => $request->gender,
        'birthday' => $request->birthday,
        'password' => Hash::make($request->password),
        'role' => 'clinic',
    ]);

    // Save clinic info
    ClinicInfo::create([
        'user_id' => $user->id,
        'clinic_name' => $clinicData['clinic_name'],
        'address' => $clinicData['address'],
        'contact_number' => $clinicData['contact_number'],
        'profile_picture' => $clinicData['logo'],
    ]);

    // Clear session data
    session()->forget('clinic_info');

    // Redirect with detailed success message
    return redirect()->route('admin.usermag', ['category' => 'clinic'])->with('registration_success', [
        'title' => 'Clinic Registration Successful!',
    ]);
}

}
