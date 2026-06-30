<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArmAssignment extends Model
{
    protected $fillable = [

        'arm_id',

        'user_id',

        'branch_id',

        'issued_at',

        'returned_at',

        'issued_by',

        'received_by',

        'reference_no',

        'condition_before',

        'condition_after',

        'ammo_issued',

        'ammo_returned',

        'ammo_remarks',

        'remarks'

    ];

    protected $casts = [

        'issued_at' => 'datetime',

        'returned_at' => 'datetime',

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCurrent($query)
    {
        return $query->whereNull('returned_at');
    }

    public function scopeReturned($query)
    {
        return $query->whereNotNull('returned_at');
    }

    public function scopeIssuedToday($query)
    {
        return $query->whereDate('issued_at', today());
    }

    public function scopeReturnedToday($query)
    {
        return $query->whereDate('returned_at', today());
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsReturnedAttribute()
    {
        return !is_null($this->returned_at);
    }

    public function getDutyHoursAttribute()
    {
        if (!$this->returned_at) {
            return now()->diffInHours($this->issued_at);
        }

        return $this->returned_at->diffInHours($this->issued_at);
    }

    public function getDutyDurationAttribute()
    {
        if (!$this->returned_at) {
            return $this->issued_at->diffForHumans();
        }

        return $this->issued_at->diffForHumans(
            $this->returned_at,
            true
        );
    }

    public function getAmmoConsumedAttribute()
    {
        return max(
            0,
            $this->ammo_issued - $this->ammo_returned
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Return firearm
     */
    public function returnFirearm(array $data = [])
    {
        $this->update([

            'returned_at' => now(),

            'received_by' => auth()->user()->name,

            'condition_after' => $data['condition_after'] ?? 'Good',

            'ammo_returned' => $data['ammo_returned'] ?? 0,

            'ammo_remarks' => $data['ammo_remarks'] ?? null,

            'remarks' => $data['remarks'] ?? $this->remarks,

        ]);

        if ($this->condition_after == 'Needs Repair') {

            $this->arm->markMaintenance();

        } else {

            $this->arm->markAvailable();

        }

        return $this;
    }

    /**
     * Extend assignment remarks
     */
    public function appendRemarks($text)
    {
        $remarks = trim($this->remarks);

        $remarks .= "\n";
        $remarks .= now()->format('Y-m-d H:i');
        $remarks .= ' - ';
        $remarks .= $text;

        $this->update([
            'remarks' => trim($remarks)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Helpers
    |--------------------------------------------------------------------------
    */

    public static function currentlyIssued()
    {
        return static::current()->count();
    }

    public static function issuedTodayCount()
    {
        return static::issuedToday()->count();
    }

    public static function returnedTodayCount()
    {
        return static::returnedToday()->count();
    }
}
