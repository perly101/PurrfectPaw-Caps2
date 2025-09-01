<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Display a listing of all appointments for the doctor.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        // Initialize query for doctor's appointments
        $query = Appointment::where('doctor_id', $doctor->id);
        
        // Handle status filtering
        if (request()->has('status') && request()->get('status')) {
            if (request()->get('status') == 'show_all') {
                // Show all statuses
            } else {
                // Filter by specific status
                $query->where('status', request()->get('status'));
            }
        } else {
            // By default, exclude cancelled and closed appointments
            $query->whereNotIn('status', ['cancelled', 'closed']);
        }
        
        // Apply date filter if provided
        if (request()->has('date') && request()->get('date')) {
            $query->whereDate('appointment_date', request()->get('date'));
        }
            
        // Get the appointments
        $appointments = $query->orderByRaw("CASE 
                WHEN status = 'assigned' THEN 1
                WHEN status = 'confirmed' THEN 2
                WHEN status = 'in_progress' THEN 3
                WHEN status = 'completed' THEN 4
                ELSE 5 END")
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);
            
        return view('doctor.appointments.index', compact('appointments', 'doctor'));
    }
    
    /**
     * Display a specific appointment details.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        // Get the appointment, ensuring it belongs to this doctor
        $appointment = Appointment::where('doctor_id', $doctor->id)
            ->with('customValues.field')
            ->findOrFail($id);
            
        // Get patient history (previous appointments for this patient)
        $patientHistory = Appointment::where('doctor_id', $doctor->id)
            ->where('id', '!=', $appointment->id)
            ->where('owner_phone', $appointment->owner_phone)
            ->orderBy('appointment_date', 'desc')
            ->get();
            
        return view('doctor.appointments.show', compact('appointment', 'doctor', 'patientHistory'));
    }
    
    /**
     * Update the status of an appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        // Get the appointment, ensuring it belongs to this doctor
        $appointment = Appointment::where('doctor_id', $doctor->id)
            ->findOrFail($id);
            
        $request->validate([
            'status' => 'required|in:assigned,confirmed,in_progress,completed,closed,cancelled',
        ]);
        
        $appointment->status = $request->status;
        $appointment->save();
        
        return redirect()->route('doctor.appointments.show', $id)
            ->with('success', 'Appointment status updated successfully');
    }
    
    /**
     * Accept or decline an appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptDecline(Request $request, $id)
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        // Get the appointment, ensuring it belongs to this doctor
        $appointment = Appointment::where('doctor_id', $doctor->id)
            ->findOrFail($id);
            
        $request->validate([
            'action' => 'required|in:accept,decline',
        ]);
        
        if ($request->action === 'accept') {
            $appointment->status = 'confirmed';
            $message = 'Appointment accepted successfully';
        } else {
            $appointment->status = 'cancelled';
            // Store cancellation reason in the notes field as JSON
            $cancellationData = ['cancellation_reason' => 'Declined by doctor', 'declined_at' => now()->format('Y-m-d H:i:s')];
            $appointment->notes = json_encode($cancellationData);
            $message = 'Appointment declined successfully';
        }
        
        $appointment->save();
        
        return redirect()->route('doctor.appointments.index')
            ->with('success', $message);
    }
    
    /**
     * Start a consultation - update status to in_progress.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startConsultation($id)
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        // Get the appointment, ensuring it belongs to this doctor
        $appointment = Appointment::where('doctor_id', $doctor->id)
            ->findOrFail($id);
            
        if ($appointment->status !== 'confirmed') {
            return redirect()->back()
                ->with('error', 'Only confirmed appointments can be started');
        }
        
        $appointment->status = 'in_progress';
        $appointment->save();
        
        return redirect()->route('doctor.appointments.show', $id)
            ->with('success', 'Consultation started successfully');
    }
    
    /**
     * Complete a consultation and add notes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completeConsultation(Request $request, $id)
    {
        // Get the current doctor
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        
        // Get the appointment, ensuring it belongs to this doctor
        $appointment = Appointment::where('doctor_id', $doctor->id)
            ->findOrFail($id);
            
        $request->validate([
            'chief_complaint' => 'required|string',
            'history_observations' => 'required|string',
            'examination_findings' => 'required|string',
            'diagnosis' => 'required|string',
            'plan_recommendations' => 'required|string',
        ]);
        
        // Compile consultation notes in a structured format
        $consultationNotes = [
            'chief_complaint' => $request->chief_complaint,
            'history_observations' => $request->history_observations,
            'examination_findings' => $request->examination_findings,
            'diagnosis' => $request->diagnosis,
            'plan_recommendations' => $request->plan_recommendations,
            'completed_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
        
        // Store as JSON using the correct column name (notes instead of consultation_notes)
        $appointment->notes = json_encode($consultationNotes);
        $appointment->status = 'closed';
        $appointment->save();
        
        return redirect()->route('doctor.appointments.index')
            ->with('success', 'Consultation completed and notes saved successfully');
    }
}
