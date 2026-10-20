<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Areas assigned to this region.
     */
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    /**
     * All current and historical pay rates.
     */
    public function payRates()
    {
        return $this->hasMany(PayRate::class)
            ->orderByDesc('effective_from');
    }

    /**
     * Rate applicable today.
     */
    public function currentPayRate()
    {
        return $this->hasOne(PayRate::class)
            ->effectiveOn(now()->toDateString())
            ->ofMany('effective_from', 'max');
    }

    /**
     * Find the rate applicable on a particular date.
     */
    public function payRateOn($date)
    {
        return $this->payRates()
            ->effectiveOn($date)
            ->first();
    }
}
