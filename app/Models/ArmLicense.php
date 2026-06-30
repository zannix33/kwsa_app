<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArmLicense extends Model
{
    protected $fillable = [

        'arm_id',

        'license_number',

        'registration_number',

        'permit_number',

        'license_type',

        'issue_date',

        'expiry_date',

        'renewed_at',

        'next_renewal',

        'issued_by',

        'status',

        'document',

        'remarks'

    ];

    protected $casts = [

        'issue_date' => 'date',

        'expiry_date' => 'date',

        'renewed_at' => 'date',

        'next_renewal' => 'date',

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
        static::creating(function ($license) {

            $license->syncStatus();

        });

        static::updating(function ($license) {

            $license->syncStatus();

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status','Active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status','Expired');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->whereBetween(
            'expiry_date',
            [
                today(),
                today()->addDays($days)
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsExpiredAttribute()
    {
        return today()->gt($this->expiry_date);
    }

    public function getIsActiveAttribute()
    {
        return !$this->is_expired;
    }

    public function getDaysRemainingAttribute()
    {
        return today()->diffInDays(
            $this->expiry_date,
            false
        );
    }

    public function getExpiresSoonAttribute()
    {
        return $this->days_remaining <= 30
            && !$this->is_expired;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function syncStatus()
    {
        if ($this->expiry_date < today()) {

            $this->status = 'Expired';

        } else {

            $this->status = 'Active';

        }
    }

    /**
     * Renew this license.
     *
     * Creates a NEW license record.
     */
    public function renew($newData)
    {
        return static::create([

            'arm_id' => $this->arm_id,

            'license_number' => $newData['license_number'],

            'registration_number' => $newData['registration_number'],

            'permit_number' => $newData['permit_number'],

            'license_type' => $this->license_type,

            'issue_date' => $newData['issue_date'],

            'expiry_date' => $newData['expiry_date'],

            'renewed_at' => now(),

            'next_renewal' => $newData['expiry_date'],

            'issued_by' => $newData['issued_by'],

            'document' => $newData['document'] ?? null,

            'remarks' => $newData['remarks'] ?? null

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Helpers
    |--------------------------------------------------------------------------
    */

    public static function activeCount()
    {
        return static::active()->count();
    }

    public static function expiredCount()
    {
        return static::expired()->count();
    }

    public static function expiring30Days()
    {
        return static::expiring(30)->count();
    }

    public static function expiring60Days()
    {
        return static::expiring(60)->count();
    }

    public static function expiring90Days()
    {
        return static::expiring(90)->count();
    }

}
