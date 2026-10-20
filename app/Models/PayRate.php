<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayRate extends Model
{
    protected $table = 'pay_rates';

    protected $fillable = [
        'region_id',
        'daily_rate',
        'effective_from',
        'effective_to',
        'wage_order_reference',
        'remarks',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    /**
     * Region this pay rate belongs to.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Check whether this rate applies on a given date.
     */
    public function isEffectiveOn($date)
    {
        $date = \Carbon\Carbon::parse($date)->toDateString();

        return $this->effective_from->toDateString() <= $date
            && (
                is_null($this->effective_to)
                || $this->effective_to->toDateString() >= $date
            );
    }

    /**
     * Scope: rates effective on a given date.
     */
    public function scopeEffectiveOn($query, $date)
    {
        $date = \Carbon\Carbon::parse($date)->toDateString();

        return $query
            ->whereDate('effective_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $date);
            });
    }
}
