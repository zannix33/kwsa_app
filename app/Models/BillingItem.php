<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_id',
        'branch_id',
        'user_id',
        'payroll_item_id',
        'code',
        'type',
        'description',
        'quantity',
        'unit',
        'rate',
        'amount',
        'calculation_details',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:4',
        'amount' => 'decimal:2',
        'calculation_details' => 'array',
    ];

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }
}
