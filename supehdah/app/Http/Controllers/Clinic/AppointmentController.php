<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicInfo;
use App\Models\Doctor;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        $appointments = Appointment::where('clinic_id', $clinic->id)
            ->with('doctor') // Eager load doctor relationship
            ->orderByRaw("CASE 
                WHEN status = 'pending' THEN 1
                WHEN status = 'assigned' THEN 2
                WHEN status = 'confirmed' THEN 3
                WHEN status = 'completed' THEN 4
                WHEN status = 'closed' THEN 5
                WHEN status = 'cancelled' THEN 6
                ELSE 7 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('clinic.appointments.index', compact('appointments', 'clinic'));
    }
    
    /**
     * Delete an appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        $appointment = Appointment::where('clinic_id', $clinic->id)->findOrFail($id);
        
        // Log the appointment info before deletion for debugging
        \Illuminate\Support\Facades\Log::info('Deleting appointment', [
            'id' => $appointment->id,
            'date' => $appointment->appointment_date,
            'time' => $appointment->appointment_time,
            'status' => $appointment->status,
        ]);
        
        // Delete the appointment
        $appointment->delete();
        
        return redirect()->route('clinic.appointments.index')
            ->with('success', 'Appointment has been deleted successfully and the slot is now available for booking.');
    }
    
    /**
     * Show the form for viewing an appointment.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        $appointment = Appointment::where('clinic_id', $clinic->id)
            ->with(['customValues.field'])
            ->findOrFail($id);
            
        return view('clinic.appointments.show', compact('appointment', 'clinic'));
    }
    
    /**
     * Update the specified appointment status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        $appointment = Appointment::where('clinic_id', $clinic->id)->findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,assigned,confirmed,completed,closed,cancelled',
        ]);
        
        // If changing to completed status, validate that a doctor is assigned
        if ($request->status === 'completed' && !$appointment->doctor_id) {
            return back()->withErrors(['doctor' => 'An appointment must be assigned to a doctor before marking as completed.']);
        }
        
        $oldStatus = $appointment->status;
        $appointment->status = $request->status;
        $appointment->save();
        
        // If the appointment is marked as completed, send a notification
        if ($request->status === 'completed' && $oldStatus !== 'completed') {
            try {
                $notificationService = app(NotificationService::class);
                $notificationService->notifyClinicAppointmentCompleted($clinic, $appointment);
            } catch (\Exception $e) {
                // Log the error but don't prevent the status update
                Log::error('Failed to send appointment completion notification: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('clinic.appointments.show', $id)
            ->with('success', 'Appointment status updated successfully');
    }
    
    /**
     * Assign a doctor to an appointment
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignDoctor(Request $request, $id)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        $appointment = Appointment::where('clinic_id', $clinic->id)->findOrFail($id);
        
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id'
        ]);
        
        $appointment->doctor_id = $request->doctor_id;
        
        // If the appointment is in pending status, update it to assigned
        if ($appointment->status === 'pending') {
            $appointment->status = 'assigned';
        }
        
        $appointment->save();
        
        // Send notification to the doctor about the new patient assignment
        $doctor = Doctor::find($request->doctor_id);
        if ($doctor && $doctor->user && $appointment->user) {
            $notificationService = app(NotificationService::class);
            $notificationService->notifyDoctorPatientAssigned($doctor->user, $appointment->user);
        }
        
        return redirect()->route('clinic.appointments.show', $id)
            ->with('success', 'Doctor assigned to appointment successfully');
    }
    
    /**
     * Add consultation notes to a completed appointment
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addNotes(Request $request, $id)
    {
        $clinic = ClinicInfo::where('user_id', Auth::id())->firstOrFail();
        $appointment = Appointment::where('clinic_id', $clinic->id)->findOrFail($id);
        
        $request->validate([
            'consultation_notes' => 'required|string'
        ]);
        
        $appointment->consultation_notes = $request->consultation_notes;
        
        // If the appointment is in completed status, update it to closed
        if ($appointment->status === 'completed') {
            $appointment->status = 'closed';
        }
        
        $appointment->save();
        
        return redirect()->route('clinic.appointments.show', $id)
            ->with('success', 'Consultation notes added successfully');
    }
}
