<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyTimeRecord extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'work_date',
        'branch_id',
        'area_id',

        'operation_start',
        'operation_end',

        'time_in',
        'time_out',

        'break_minutes',

        'scheduled_hours',

        'regular_hours',
        'overtime_hours',
        'extended_hours',
        'night_differential_hours',

        'late_hours',
        'undertime_hours',

        'total_hours',

        'is_rest_day',
        'is_holiday',
        'special_holiday',
        'is_extended_hours',

        'remarks',
    ];

    protected $casts = [
        'work_date' => 'date',
        'operation_start' => 'datetime',
        'operation_end' => 'datetime',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
     public function getFormattedTimeInAttribute()
    {
        return $this->time_in
            ? $this->time_in->format('M d, Y h:i A')
            : '';
    }

    public function getFormattedTimeOutAttribute()
    {
        return $this->time_out
            ? $this->time_out->format('M d, Y h:i A')
            : '';
    }
    */

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
