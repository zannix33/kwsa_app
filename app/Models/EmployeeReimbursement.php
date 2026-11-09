<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeReimbursement extends Model
{
    protected $fillable = ['user_id', 'payroll_period_id', 'payroll_id', 'filed_date', 'category', 'description', 'amount', 'receipt_path', 'status'];
    protected $casts = ['filed_date' => 'date', 'amount' => 'decimal:2'];

    public function user() { return $this->belongsTo(User::class); }
    public function payrollPeriod() { return $this->belongsTo(PayrollPeriod::class); }
    public function payroll() { return $this->belongsTo(Payroll::class); }
}
