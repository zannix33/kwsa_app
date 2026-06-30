<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArmInspection extends Model
{
    protected $fillable = [

        'arm_id',

        'inspection_date',

        'inspector',

        'inspection_type',

        'barrel_condition',
        'slide_condition',
        'frame_condition',
        'trigger_condition',
        'magazine_condition',
        'sight_condition',

        'overall_condition',

        'result',

        'findings',

        'recommendation',

        'requires_maintenance',

        'next_inspection',

        'remarks'

    ];

    protected $casts = [

        'inspection_date' => 'date',

        'next_inspection' => 'date',

        'requires_maintenance' => 'boolean'

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
        static::creating(function ($inspection) {

            $inspection->evaluate();

        });

        static::updating(function ($inspection) {

            $inspection->evaluate();

        });

        static::created(function ($inspection) {

            $inspection->updateFirearmStatus();

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePassed($query)
    {
        return $query->where('result','Passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('result','Failed');
    }

    public function scopeDue($query)
    {
        return $query->whereDate(
            'next_inspection',
            '<=',
            today()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsPassedAttribute()
    {
        return $this->result == 'Passed';
    }

    public function getIsFailedAttribute()
    {
        return $this->result == 'Failed';
    }

    public function getDaysUntilInspectionAttribute()
    {
        if (!$this->next_inspection) {

            return null;

        }

        return today()->diffInDays(
            $this->next_inspection,
            false
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Determine inspection result automatically
     */
    public function evaluate()
    {
        if (
            in_array($this->overall_condition, [
                'Needs Repair',
                'Unserviceable'
            ])
        ) {

            $this->result = 'Failed';

            $this->requires_maintenance = true;

        } else {

            $this->result = 'Passed';

            $this->requires_maintenance = false;

        }

        if (!$this->next_inspection) {

            $this->next_inspection =
                now()->addMonths(6);

        }
    }

    /**
     * Update firearm status
     */
    public function updateFirearmStatus()
    {
        if ($this->overall_condition == 'Unserviceable') {

            $this->arm->retire();

            return;
        }

        if ($this->requires_maintenance) {

            $this->arm->markMaintenance();

            return;
        }

        $this->arm->markAvailable();
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Helpers
    |--------------------------------------------------------------------------
    */

    public static function passedCount()
    {
        return static::passed()->count();
    }

    public static function failedCount()
    {
        return static::failed()->count();
    }

    public static function dueCount()
    {
        return static::due()->count();
    }

    public static function maintenanceRequiredCount()
    {
        return static::where(
            'requires_maintenance',
            true
        )->count();
    }

}
