<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClinicInfoController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\MeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\ClinicGalleryController;
use App\Http\Controllers\Api\ClinicFieldApiController;
use App\Http\Controllers\Api\ClinicAppointmentApiController;
use App\Http\Controllers\Api\ClinicHomepageApiController;   
use App\Http\Controllers\API\AppointmentApiController;
use App\Http\Controllers\API\ClinicStatusController;
use App\Http\Controllers\API\AvailabilityApiController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // OTP Verification API Routes with rate limiting
    // Maximum 5 attempts per minute for OTP verification
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/verify-otp', [App\Http\Controllers\API\OtpVerificationController::class, 'verify']);
        Route::post('/resend-otp', [App\Http\Controllers\API\OtpVerificationController::class, 'resend']);
    });
    
    Route::get('/user/email-verified', [App\Http\Controllers\API\OtpVerificationController::class, 'checkVerified']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'user']);
    Route::put('/me', [AuthController::class, 'updateProfile']);
});

//clinic mode
Route::get('/clinics', [ClinicController::class, 'index']);
Route::get('/clinic-status/{id}', [ClinicStatusController::class, 'getStatus']);

Route::get('/clinics/{clinic}/gallery', [ClinicGalleryController::class, 'index']);

//clinic mode  appointment

Route::get('/clinics/{clinic}/fields', [ClinicFieldApiController::class, 'index']);

// Appointment routes
Route::middleware('refresh.appointment')->group(function () {
    Route::post('/clinics/{clinicId}/appointments', [AppointmentApiController::class, 'store']);
});

// Pet API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/pets', [App\Http\Controllers\Api\PetApiController::class, 'index']);
    Route::post('/pets', [App\Http\Controllers\Api\PetApiController::class, 'store']);
    Route::get('/pets/{id}', [App\Http\Controllers\Api\PetApiController::class, 'show']);
    Route::put('/pets/{id}', [App\Http\Controllers\Api\PetApiController::class, 'update']);
    Route::delete('/pets/{id}', [App\Http\Controllers\Api\PetApiController::class, 'destroy']);
    
    // Pet vaccination routes
    Route::post('/pets/{id}/vaccinations', [App\Http\Controllers\Api\PetApiController::class, 'storeVaccination']);
    Route::get('/pets/{id}/vaccinations', [App\Http\Controllers\Api\PetApiController::class, 'getVaccinations']);
});
Route::get('/clinics/{clinicId}/appointments', [AppointmentApiController::class, 'index']);
Route::get('/clinics/{clinicId}/appointments/{id}', [AppointmentApiController::class, 'show']);
Route::put('/clinics/{clinicId}/appointments/{id}/status', [AppointmentApiController::class, 'updateStatus']);
Route::delete('/clinics/{clinicId}/appointments/{id}', [AppointmentApiController::class, 'destroy']);

Route::get('/clinics/{clinic}/homepage', [ClinicHomepageApiController::class, 'show']);

// Availability API Routes
Route::middleware('refresh.appointment')->group(function () {
    Route::get('/clinics/{clinicId}/availability/settings', [AvailabilityApiController::class, 'getSettings']);
    Route::get('/clinics/{clinicId}/availability/daily-schedule/{dayOfWeek}', [AvailabilityApiController::class, 'getDailySchedule']);
    Route::get('/clinics/{clinicId}/availability/breaks', [AvailabilityApiController::class, 'getBreaks']);
    Route::get('/clinics/{clinicId}/availability/special-dates', [AvailabilityApiController::class, 'getSpecialDates']);
    Route::get('/clinics/{clinicId}/availability/slots/{date}', [AvailabilityApiController::class, 'getAvailableSlots']);
    Route::get('/clinics/{clinicId}/availability/summary', [AvailabilityApiController::class, 'getAvailabilitySummary']);
    Route::get('/clinics/{clinicId}/availability/dates', [AvailabilityApiController::class, 'getCalendarDates']);
});

//extra

Route::middleware('auth:sanctum')->get('/me', MeController::class);

Route::get('/user', [AuthController::class, 'user']);

Route::get('/clinics', [ClinicInfoController::class, 'index']);

Route::middleware('auth:sanctum')->put('/user', [UserController::class, 'update']);

// Test route for appointment date/time handling (non-production only)
if (app()->environment(['local', 'development', 'testing'])) {
    Route::get('/test-appointment', [App\Http\Controllers\TestAppointmentController::class, 'createTestAppointment']);
}

Route::middleware('auth:sanctum')->put('/update-profile', [AuthController::class, 'updateProfile']);

