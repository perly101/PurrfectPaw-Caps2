<?php

// app/Models/Clinic.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Clinic extends Authenticatable
{
    use Notifiable;

    // 🔧 Tell Laravel to use the custom table name
    protected $table = 'clinic_infos';

    protected $fillable = [
        'profile_picture', // instead of 'logo' if you're using 'profile_picture'
        'clinic_name',
        'address',
        'contact_number',
        'email',
        'password',
        'user_id',
    ];

    protected $hidden = [
        'password',
    ];
    
    /**
     * Get the notifications for this clinic.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
