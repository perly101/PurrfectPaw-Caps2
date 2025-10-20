<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'clinic_id',
        'plan_type',
        'amount',
        'billing_cycle',
        'status',
        'start_date',
        'end_date',
        'next_billing_date',
        'payment_method',
        'payment_reference',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'next_billing_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the clinic that owns the subscription.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
}