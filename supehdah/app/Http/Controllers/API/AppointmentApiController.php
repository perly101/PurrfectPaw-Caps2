<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentFieldValue;
use App\Models\ClinicField;
use App\Models\ClinicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentApiController extends Controller
{
    /**
     * Store a new appointment from the mobile app
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $clinicId)
    {
        // Validate clinic exists
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        // Basic validation
        $validator = Validator::make($request->all(), [
            'owner_name' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
            'responses' => 'required|array',
            'responses.*.field_id' => 'required|exists:clinic_fields,id',
            'responses.*.value' => 'nullable',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Enhanced logging for appointment creation debugging
        \Illuminate\Support\Facades\Log::info('Creating appointment with data', [
            'date' => $request->appointment_date,
            'date_type' => gettype($request->appointment_date),
            'time' => $request->appointment_time,
            'time_type' => gettype($request->appointment_time),
            'owner' => $request->owner_name,
            'clinic_id' => $clinic->id,
            'all_data' => $request->all()
        ]);
        
        // Process appointment date/time to ensure correct format according to README
        // Dates should be in Y-m-d format, Times in H:i:s format
        $appointmentDate = $request->appointment_date;
        $appointmentTime = $request->appointment_time;
        
        // Enhanced date formatting and validation
        try {
            if ($appointmentDate) {
                // Set timezone to Philippines (UTC+8) as specified in README
                $date = \Carbon\Carbon::parse($appointmentDate, 'Asia/Manila');
                $appointmentDate = $date->format('Y-m-d');
                
                // Additional validation - if date seems invalid, use current date
                if ($appointmentDate === '1970-01-01' || !$date->isValid()) {
                    \Illuminate\Support\Facades\Log::warning('Invalid appointment date detected, using current date', [
                        'original' => $request->appointment_date,
                        'parsed' => $appointmentDate
                    ]);
                    $appointmentDate = \Carbon\Carbon::now('Asia/Manila')->format('Y-m-d');
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error parsing appointment date', [
                'date' => $appointmentDate,
                'error' => $e->getMessage()
            ]);
            // Fall back to current date if parsing fails
            $appointmentDate = \Carbon\Carbon::now('Asia/Manila')->format('Y-m-d');
        }
        
        // Enhanced time formatting and validation
        try {
            if ($appointmentTime) {
                // Set timezone to Philippines (UTC+8) as specified in README
                $time = \Carbon\Carbon::parse($appointmentTime, 'Asia/Manila');
                $appointmentTime = $time->format('H:i:s');
                
                // Additional validation - if time seems invalid, log warning
                if ($appointmentTime === '00:00:00' && $request->appointment_time !== '00:00:00') {
                    \Illuminate\Support\Facades\Log::warning('Potentially invalid appointment time', [
                        'original' => $request->appointment_time,
                        'parsed' => $appointmentTime
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error parsing appointment time', [
                'time' => $appointmentTime,
                'error' => $e->getMessage()
            ]);
            // Fall back to a sensible default time if parsing fails
            $appointmentTime = '09:00:00';
        }
        
        // Log the formatted values
        \Illuminate\Support\Facades\Log::info('Formatted appointment date/time', [
            'formatted_date' => $appointmentDate,
            'formatted_time' => $appointmentTime
        ]);
        
        // Create the appointment with properly formatted date/time
        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'owner_name' => $request->owner_name,
            'owner_phone' => $request->owner_phone,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'status' => 'pending', // Default status
        ]);
        
        // Process field responses
        foreach ($request->responses as $response) {
            $fieldId = $response['field_id'];
            $value = $response['value'];
            
            // Store the value - we're using json casting in the model
            AppointmentFieldValue::create([
                'appointment_id' => $appointment->id,
                'clinic_field_id' => $fieldId,
                'value' => $value, // Will be cast to JSON if array
            ]);
        }
        
        return response()->json([
            'message' => 'Appointment created successfully',
            'appointment_id' => $appointment->id
        ], 201);
    }
    
    /**
     * Get all appointments for a clinic
     *
     * @param  int  $clinicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($clinicId)
    {
        // Ensure clinic exists
        $clinic = ClinicInfo::findOrFail($clinicId);
        
        // Get appointments with their values
        $appointments = Appointment::where('clinic_id', $clinicId)
            ->with(['customValues.field'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'data' => $appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'owner_name' => $appointment->owner_name,
                    'owner_phone' => $appointment->owner_phone,
                    'status' => $appointment->status,
                    'created_at' => $appointment->created_at->format('Y-m-d H:i:s'),
                    'responses' => $appointment->customValues->map(function ($value) {
                        return [
                            'field_id' => $value->clinic_field_id,
                            'field_label' => $value->field->label,
                            'field_type' => $value->field->type,
                            'value' => $value->value,
                        ];
                    }),
                ];
            }),
        ]);
    }
    
    /**
     * Get a specific appointment
     *
     * @param  int  $clinicId
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($clinicId, $id)
    {
        $appointment = Appointment::where('clinic_id', $clinicId)
            ->with(['customValues.field'])
            ->findOrFail($id);
            
        return response()->json([
            'data' => [
                'id' => $appointment->id,
                'owner_name' => $appointment->owner_name,
                'owner_phone' => $appointment->owner_phone,
                'status' => $appointment->status,
                'created_at' => $appointment->created_at->format('Y-m-d H:i:s'),
                'responses' => $appointment->customValues->map(function ($value) {
                    return [
                        'field_id' => $value->clinic_field_id,
                        'field_label' => $value->field->label,
                        'field_type' => $value->field->type,
                        'value' => $value->value,
                    ];
                }),
            ],
        ]);
    }
    
    /**
     * Update appointment status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $clinicId
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $clinicId, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,confirmed,cancelled,completed',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $appointment = Appointment::where('clinic_id', $clinicId)
            ->findOrFail($id);
            
        $appointment->status = $request->status;
        $appointment->save();
        
        return response()->json([
            'message' => 'Appointment status updated',
            'status' => $appointment->status,
        ]);
    }

    /**
     * Delete an appointment
     * 
     * @param int $clinicId
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($clinicId, $id)
    {
        // Verify the appointment belongs to the clinic
        $appointment = Appointment::where('clinic_id', $clinicId)
            ->findOrFail($id);
        
        // Log the appointment info before deletion for debugging
        \Illuminate\Support\Facades\Log::info('API: Deleting appointment', [
            'id' => $appointment->id,
            'date' => $appointment->appointment_date,
            'time' => $appointment->appointment_time,
            'status' => $appointment->status,
            'clinic_id' => $clinicId
        ]);
        
        // Delete the appointment
        $appointment->delete();
        
        return response()->json([
            'message' => 'Appointment deleted successfully',
            'success' => true,
            'appointment_id' => $id,
            'date' => $appointment->appointment_date,
            'time' => $appointment->appointment_time,
        ]);
    }
}
