<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArmMaintenance extends Model
{
    protected $fillable = [

        'arm_id',

        'maintenance_date',

        'maintenance_type',

        'performed_by',

        'service_provider',

        'description',

        'parts_replaced',

        'labor_cost',

        'parts_cost',

        'total_cost',

        'condition_after',

        'next_due',

        'completed',

        'remarks'

    ];

    protected $casts = [

        'maintenance_date' => 'date',

        'next_due' => 'date',

        'completed' => 'boolean',

        'labor_cost' => 'decimal:2',

        'parts_cost' => 'decimal:2',

        'total_cost' => 'decimal:2',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function arm()
    {
        return $this->belongsTo(Arm::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($maintenance) {

            // Automatically compute total cost
            $maintenance->total_cost =
                ($maintenance->labor_cost ?? 0)
                +
                ($maintenance->parts_cost ?? 0);

        });

        static::updating(function ($maintenance) {

            $maintenance->total_cost =
                ($maintenance->labor_cost ?? 0)
                +
                ($maintenance->parts_cost ?? 0);

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('completed', false);
    }

    public function scopeDue($query)
    {
        return $query->whereDate(
            'next_due',
            '<=',
            today()
        );
    }

    public function scopeDueThisMonth($query)
    {
        return $query->whereMonth(
            'next_due',
            now()->month
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsDueAttribute()
    {
        if (!$this->next_due) {

            return false;

        }

        return $this->next_due <= today();
    }

    public function getDaysUntilDueAttribute()
    {
        if (!$this->next_due) {

            return null;

        }

        return today()->diffInDays(
            $this->next_due,
            false
        );
    }

    public function getMaintenanceCostAttribute()
    {
        return $this->labor_cost + $this->parts_cost;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Complete maintenance
     */
    public function complete()
    {
        $this->update([
            'completed' => true
        ]);

        if ($this->condition_after == 'Unserviceable') {

            $this->arm->retire();

            return;
        }

        $this->arm->markAvailable();
    }

    /**
     * Send firearm to maintenance
     */
    public static function start(array $data)
    {
        $maintenance = static::create($data);

        $maintenance->arm->markMaintenance();

        return $maintenance;
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Helpers
    |--------------------------------------------------------------------------
    */

    public static function dueCount()
    {
        return static::due()->count();
    }

    public static function pendingCount()
    {
        return static::pending()->count();
    }

    public static function completedCount()
    {
        return static::completed()->count();
    }

    public static function totalMaintenanceCost()
    {
        return static::sum('total_cost');
    }

    public static function monthlyMaintenanceCost()
    {
        return static::whereMonth(
            'maintenance_date',
            now()->month
        )->sum('total_cost');
    }
}
