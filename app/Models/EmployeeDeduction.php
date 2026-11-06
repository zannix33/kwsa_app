<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    protected $fillable = [
        'user_id', 'description', 'amount', 'frequency', 'start_date', 'end_date',
        'installment_count', 'monthly_cutoff', 'status', 'payment_status'
    ];

    protected $dates = ['start_date', 'end_date'];

    public function user() { return $this->belongsTo(User::class); }
    public function periods() { return $this->hasMany(EmployeeDeductionPeriod::class); }

    public function getProcessedCountAttribute()
    {
        return $this->periods()->where('status', 'Processed')->count();
    }
}
