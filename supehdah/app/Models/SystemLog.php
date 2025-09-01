<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'details',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new log entry.
     *
     * @param string $action The action being performed
     * @param array $details Details about the action
     * @param string $status Status of the action (success, error, warning, info)
     * @return SystemLog
     */
    public static function log($action, $details = [], $status = 'info')
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'ip_address' => request()->ip(),
            'details' => $details,
            'status' => $status,
        ]);
    }
}
