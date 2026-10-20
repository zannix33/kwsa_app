<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'loan_reference',
        'loan_provider',
        'loan_type',
        'loan_date',
        'principal_amount',
        'interest_amount',
        'total_loan_amount',
        'monthly_amortization',
        'total_paid',
        'outstanding_balance',
        'deduction_start_date',
        'deduction_end_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'deduction_start_date' => 'date',
        'deduction_end_date' => 'date',
        'principal_amount' => 'decimal:2',
        'monthly_amortization' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }
}
