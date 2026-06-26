<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_from',
        'date_to',
        'status',
        'processed_at'
    ];

    protected $dates = [
        'date_from',
        'date_to',
        'processed_at'
    ];

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function getEmployeeCountAttribute()
    {
        return $this->payrolls()->count();
    }

    public function getTotalNetPayAttribute()
    {
        return $this->payrolls()->sum('net_pay');
    }
}
