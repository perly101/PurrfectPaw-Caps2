<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClinicSpecialDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'date',
        'is_closed',
        'start_time',
        'end_time',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'is_closed' => 'boolean',
    ];

    public function clinic()
    {
        return $this->belongsTo(ClinicInfo::class, 'clinic_id');
    }
}
