<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [

        'payroll_period_id',
        'user_id',

        'gross_pay',
        'total_earnings',
        'total_deductions',
        'net_pay',

        'status'
    ];

    public function period()
    {
        return $this->belongsTo(
            PayrollPeriod::class,
            'payroll_period_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }
}
