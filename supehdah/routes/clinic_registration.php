<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClinicRegistrationController;
use App\Http\Controllers\FakePaymentController;

// Clinic self-registration routes
Route::get('/register-clinic/plan/{plan?}', [ClinicRegistrationController::class, 'showStep1'])->name('clinic.register.select-plan');
Route::post('/register-clinic/submit-step1', [ClinicRegistrationController::class, 'storeStep1'])->name('clinic.register.step1.store');
Route::get('/register-clinic/step2', [ClinicRegistrationController::class, 'showStep2'])->name('clinic.register.step2');
Route::post('/register-clinic/submit-step2', [ClinicRegistrationController::class, 'storeStep2'])->name('clinic.register.step2.store');

// Email verification routes
use App\Http\Controllers\ClinicVerificationController;
Route::get('/register-clinic/verify', [ClinicVerificationController::class, 'showVerificationForm'])->name('clinic.register.verification');
Route::post('/register-clinic/verify', [ClinicVerificationController::class, 'verifyOtp'])->name('clinic.register.verify');
Route::post('/register-clinic/verify/resend', [ClinicVerificationController::class, 'resendOtp'])->name('clinic.register.resend');

// Payment routes
Route::get('/payment', [FakePaymentController::class, 'show'])->name('payment.show');
Route::post('/payment/process', [FakePaymentController::class, 'process'])->name('payment.process');
Route::get('/registration/thank-you', [FakePaymentController::class, 'thankYou'])->name('payment.thank-you');
Route::get('/registration/thank-you-pending', [FakePaymentController::class, 'thankYouPending'])->name('payment.thank-you.pending');
Route::get('/registration/check-status', [FakePaymentController::class, 'checkStatus'])->name('payment.check-status');