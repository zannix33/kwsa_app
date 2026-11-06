<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeductionPeriod extends Model
{
    protected $fillable = ['employee_deduction_id', 'payroll_period_id', 'payroll_id', 'amount', 'status', 'processed_at'];
    protected $dates = ['processed_at'];
    public function deduction() { return $this->belongsTo(EmployeeDeduction::class, 'employee_deduction_id'); }
    public function period() { return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id'); }
    public function payroll() { return $this->belongsTo(Payroll::class); }
}
