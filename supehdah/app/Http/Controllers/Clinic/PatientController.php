<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\ClinicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display a listing of patients.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        
        if (!$clinic) {
            return redirect()->route('clinic.dashboard')->with('error', 'Clinic information not found.');
        }
        
        // Get all appointments for the clinic with patient information
        $patients = DB::table('appointments')
            ->select(
                'appointments.id as patient_id', 
                'appointments.owner_name as name',
                'appointments.owner_phone as phone',
                DB::raw('COUNT(appointments.id) as total_appointments'),
                DB::raw('COUNT(CASE WHEN appointments.status = "completed" THEN 1 END) as completed'),
                DB::raw('COUNT(CASE WHEN appointments.status = "cancelled" THEN 1 END) as cancelled'),
                DB::raw('COUNT(CASE WHEN appointments.status = "pending" THEN 1 END) as pending'),
                DB::raw('COUNT(CASE WHEN appointments.status = "confirmed" THEN 1 END) as confirmed'),
                DB::raw('COUNT(CASE WHEN appointments.status = "closed" THEN 1 END) as closed'),
                DB::raw('MAX(appointments.created_at) as last_appointment')
            )
            ->where('appointments.clinic_id', $clinic->id)
            ->groupBy('appointments.owner_name', 'appointments.owner_phone')
            ->orderBy('last_appointment', 'desc')
            ->paginate(10);
            
        return view('clinic.patients.index', compact('patients', 'clinic'));
    }

    /**
     * Display the specified patient with their appointment history.
     *
     * @param int $patientId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($patientId)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->first();
        
        if (!$clinic) {
            return redirect()->route('clinic.dashboard')->with('error', 'Clinic information not found.');
        }
        
        // Get the original appointment to get patient contact information
        $originalAppointment = Appointment::where('id', $patientId)
                                          ->where('clinic_id', $clinic->id)
                                          ->first();
                               
        if (!$originalAppointment) {
            return redirect()->route('clinic.patients.index')->with('error', 'Patient not found.');
        }
        
        // Get basic patient info from the original appointment
        $patientInfo = $originalAppointment;
        
        // Get all appointments for this patient by matching contact info
        $appointments = Appointment::where('owner_name', $originalAppointment->owner_name)
                                 ->where('owner_phone', $originalAppointment->owner_phone)
                                 ->where('clinic_id', $clinic->id)
                                 ->with('doctor') // Load doctor relationship
                                 ->orderBy('created_at', 'desc')
                                 ->get();
        
        return view('clinic.patients.show', compact('patientInfo', 'appointments', 'patientId', 'clinic'));
    }
}
