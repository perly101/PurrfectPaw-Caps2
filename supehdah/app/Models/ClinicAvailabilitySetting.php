<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClinicAvailabilitySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'daily_limit',
        'slot_duration',
        'default_start_time',
        'default_end_time',
    ];

    public function clinic()
    {
        return $this->belongsTo(ClinicInfo::class, 'clinic_id');
    }
}
