<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PayrollRate;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'rate',
        'region_id',

        // Billing contract rates (separate from payroll rates).
        'billing_half_month_rate_per_guard',
        'billing_additional_post_daily_rate',
        'billing_regular_ot_multiplier',
        'billing_special_ot_multiplier',
        'billing_regular_holiday_ot_multiplier',
        'billing_night_diff_percent',
        'billing_agency_fee_percent',
        'billing_ewt_percent',
        'billing_retirement_fund_amount',
        'billing_retirement_fund_percent',
        'billing_penalty_amount',
        'billing_tax_type',
    ];

    protected $casts = [
        'billing_half_month_rate_per_guard' => 'decimal:2',
        'billing_additional_post_daily_rate' => 'decimal:2',
        'billing_regular_ot_multiplier' => 'decimal:4',
        'billing_special_ot_multiplier' => 'decimal:4',
        'billing_regular_holiday_ot_multiplier' => 'decimal:4',
        'billing_night_diff_percent' => 'decimal:4',
        'billing_agency_fee_percent' => 'decimal:4',
        'billing_ewt_percent' => 'decimal:4',
        'billing_retirement_fund_amount' => 'decimal:2',
        'billing_retirement_fund_percent' => 'decimal:4',
        'billing_penalty_amount' => 'decimal:2',
    ];

    protected $appends = [
        //'payroll_rate',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    //public function getPayrollRateAttribute()
    //{
    //    $rate = PayrollRate::where(
    //        'slug',
    //        ($this->rate ? $this->rate : 'ncr')
    //    )->first()?->rate;

    //    return $rate;
    //}

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function dtrs()
    {
        return $this->hasMany(DailyTimeRecord::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
