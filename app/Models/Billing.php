<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'area_id',
        'payroll_period_id',
        'invoice_number',
        'status',
        'subtotal',
        'agency_fee_amount',
        'retirement_fund_amount',
        'penalties_amount',
        'ewt_amount',
        'vat_amount',
        'total_amount',
        'tax_type',
        'billing_date',
        'due_date',
        'notes',
        'calculation_snapshot',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'agency_fee_amount' => 'decimal:2',
        'retirement_fund_amount' => 'decimal:2',
        'penalties_amount' => 'decimal:2',
        'ewt_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_date' => 'date',
        'due_date' => 'date',
        'calculation_snapshot' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillingItem::class);
    }
}
