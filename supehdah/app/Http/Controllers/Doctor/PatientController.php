<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display a listing of the doctor's patients.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        // Get unique patients by grouping appointments
        $patients = Appointment::where('doctor_id', $doctor->id)
            ->select('owner_name', 'owner_phone')
            ->distinct()
            ->orderBy('owner_name')
            ->paginate(20);
            
        return view('doctor.patients.index', compact('patients', 'doctor'));
    }
    
    /**
         * Display patient details and history.
         *
         * @param  string  $phone
         * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
         */
        public function show($phone)
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        // Get a sample appointment to get patient details
        $patientInfo = Appointment::where('doctor_id', $doctor->id)
            ->where('owner_phone', $phone)
            ->first();
            
        if (!$patientInfo) {
            return redirect()->route('doctor.patients.index')
                ->with('error', 'Patient not found');
        }
        
        // Get all appointments for this patient
        $appointments = Appointment::where('doctor_id', $doctor->id)
            ->where('owner_phone', $phone)
            ->orderBy('appointment_date', 'desc')
            ->get();
            
        return view('doctor.patients.show', compact('patientInfo', 'appointments', 'doctor'));
    }
}
