<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Models\ClinicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Display a listing of the doctors.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        $doctors = Doctor::where('clinic_id', $clinic->id)->orderBy('created_at', 'desc')->get();
        
        return view('clinic.doctors.index', compact('doctors', 'clinic'));
    }

    /**
     * Show the form for creating a new doctor.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('clinic.doctors.create');
    }

    /**
     * Store a newly created doctor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,prefer_not_say',
            'birthday' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'bio' => 'nullable|string',
            'temp_password' => 'required|min:6',
        ]);
        
        try {
            DB::beginTransaction();
            
            $clinic = ClinicInfo::where('user_id', Auth::id())->first();
            
            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('doctor_photos', 'public');
            }
            
            // Create the doctor record
            $doctor = Doctor::create([
                'clinic_id' => $clinic->id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'photo' => $photoPath,
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'experience_years' => $request->experience_years,
                'availability_status' => 'active',
                'bio' => $request->bio,
            ]);
            
            // Create user account for the doctor
            $user = User::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'password' => Hash::make($request->temp_password),
                'role' => 'doctor'
            ]);
            
            // Link user to doctor
            $doctor->user_id = $user->id;
            $doctor->save();
            
            DB::commit();
            
            return redirect()->route('clinic.doctors.index')
                ->with('success', 'Doctor added successfully. A user account has been created.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified doctor.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);
        
        // Make sure the doctor belongs to the current clinic
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        if ($doctor->clinic_id !== $clinic->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('clinic.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified doctor.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $doctor = Doctor::findOrFail($id);
        
        // Make sure the doctor belongs to the current clinic
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        if ($doctor->clinic_id !== $clinic->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('clinic.doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified doctor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        
        // Make sure the doctor belongs to the current clinic
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        if ($doctor->clinic_id !== $clinic->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id . '|unique:users,email,' . $doctor->user_id,
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,prefer_not_say',
            'birthday' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'bio' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($doctor->photo) {
                    Storage::disk('public')->delete($doctor->photo);
                }
                
                $photoPath = $request->file('photo')->store('doctor_photos', 'public');
                $doctor->photo = $photoPath;
            }
            
            // Update doctor details
            $doctor->first_name = $request->first_name;
            $doctor->middle_name = $request->middle_name;
            $doctor->last_name = $request->last_name;
            $doctor->email = $request->email;
            $doctor->phone_number = $request->phone_number;
            $doctor->gender = $request->gender;
            $doctor->birthday = $request->birthday;
            $doctor->specialization = $request->specialization;
            $doctor->license_number = $request->license_number;
            $doctor->experience_years = $request->experience_years;
            $doctor->bio = $request->bio;
            $doctor->save();
            
            // Update related user account if it exists
            if ($doctor->user_id) {
                $user = User::find($doctor->user_id);
                if ($user) {
                    $user->first_name = $request->first_name;
                    $user->middle_name = $request->middle_name;
                    $user->last_name = $request->last_name;
                    $user->email = $request->email;
                    $user->phone_number = $request->phone_number;
                    $user->gender = $request->gender;
                    $user->birthday = $request->birthday;
                    $user->save();
                }
            }
            
            DB::commit();
            
            return redirect()->route('clinic.doctors.index')->with('success', 'Doctor information updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified doctor from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        
        // Make sure the doctor belongs to the current clinic
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        if ($doctor->clinic_id !== $clinic->id) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the doctor's photo if exists
            if ($doctor->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }
            
            // Delete associated user if exists
            if ($doctor->user_id) {
                $user = User::find($doctor->user_id);
                if ($user) {
                    $user->delete();
                }
            }
            
            // Delete the doctor record
            $doctor->delete();
            
            DB::commit();
            
            return redirect()->route('clinic.doctors.index')->with('success', 'Doctor deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Update the doctor's availability status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Update doctor availability status
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'availability_status' => 'required|in:active,on_leave,not_accepting'
        ]);
        
        $doctor = Doctor::findOrFail($id);
        
        // Make sure the doctor belongs to the current clinic
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        if ($doctor->clinic_id !== $clinic->id) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }
        
        $doctor->availability_status = $request->availability_status;
        $doctor->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Doctor availability status updated successfully.',
                'status' => $doctor->availability_status
            ]);
        }
        
        return redirect()->back()->with('success', 'Doctor availability status updated successfully.');
    }
}
