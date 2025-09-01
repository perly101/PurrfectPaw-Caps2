<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClinicDailySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_closed',
        'daily_limit',
        'slot_duration',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function clinic()
    {
        return $this->belongsTo(ClinicInfo::class, 'clinic_id');
    }
}
