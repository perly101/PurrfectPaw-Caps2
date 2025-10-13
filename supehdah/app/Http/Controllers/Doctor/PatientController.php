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

    /**
     * Get patient history for AJAX requests.
     *
     * @param  string  $phone
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory($phone)
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        // Get all appointments for this patient
        $appointments = Appointment::where('doctor_id', $doctor->id)
            ->where('owner_phone', $phone)
            ->orderBy('appointment_date', 'desc')
            ->get();
            
        // Format the appointments for the response
        $formattedAppointments = $appointments->map(function ($appointment) {
            $notes = json_decode($appointment->notes, true);
            $has_notes = !empty($notes);
            
            return [
                'id' => $appointment->id,
                'date' => \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') . 
                        ($appointment->appointment_time ? ' at ' . \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : ''),
                'status' => $appointment->status,
                'has_notes' => $has_notes,
                'diagnosis' => $has_notes && isset($notes['diagnosis']) ? $notes['diagnosis'] : null,
                'chief_complaint' => $has_notes && isset($notes['chief_complaint']) ? $notes['chief_complaint'] : null,
                'recommendations' => $has_notes && isset($notes['plan_recommendations']) ? $notes['plan_recommendations'] : null,
                'cancellation_reason' => $has_notes && isset($notes['cancellation_reason']) ? $notes['cancellation_reason'] : null,
            ];
        });
        
        // Get the date of first visit
        $first_visit_date = $appointments->isNotEmpty() ? 
            $appointments->last()->created_at->format('F d, Y') : 
            'N/A';
        
        return response()->json([
            'first_visit_date' => $first_visit_date,
            'appointments' => $formattedAppointments
        ]);
    }
}
