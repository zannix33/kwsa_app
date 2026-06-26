<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTimeRecord extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'work_date',

        'operation_start',
        'operation_end',

        'time_in',
        'time_out',

        'break_minutes',

        'scheduled_hours',

        'regular_hours',
        'overtime_hours',
        'night_differential_hours',

        'late_hours',
        'undertime_hours',

        'total_hours',

        'is_rest_day',
        'is_holiday',

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
}
