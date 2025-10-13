<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Clinic;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // 'doctor_assigned_patient', 'clinic_new_appointment', 'clinic_appointment_completed'
        'notifiable_id',
        'notifiable_type', // App\Models\User (for doctors), App\Models\Clinic (for clinics)
        'data', // JSON data containing details about the notification
        'read_at', // Timestamp when notification was read (null if unread)
        'device_token' // FCM token for push notifications
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Notifiable polymorphic relationship (can be a user/doctor or clinic)
        public function notifiable()
        {
            return $this->morphTo();
        }
    }