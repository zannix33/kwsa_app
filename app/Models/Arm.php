<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Arm extends Model
{
    protected $fillable = [
        'property_no',
        'serial_no',
        'model',
        'caliber',
        'type',
        'color',
        'purchase_date',
        'purchase_cost',
        'supplier',
        'manufacturer',
        'branch_id',
        'status',
        'remarks'
    ];

    protected $casts = [

        'purchase_date' => 'date',

        'purchase_cost' => 'decimal:2',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignments()
    {
        return $this->hasMany(ArmAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(ArmAssignment::class)
            ->whereNull('returned_at')
            ->latestOfMany();
    }

    public function maintenances()
    {
        return $this->hasMany(ArmMaintenance::class)
            ->latest('maintenance_date');
    }

    public function latestMaintenance()
    {
        return $this->hasOne(ArmMaintenance::class)
            ->latestOfMany();
    }

    public function inspections()
    {
        return $this->hasMany(ArmInspection::class)
            ->latest('inspection_date');
    }

    public function latestInspection()
    {
        return $this->hasOne(ArmInspection::class)
            ->latestOfMany();
    }

    public function licenses()
    {
        return $this->hasMany(ArmLicense::class)
            ->latest('expiry_date');
    }

    public function activeLicense()
    {
        return $this->hasOne(ArmLicense::class)
            ->where('status','Active')
            ->latestOfMany();
    }

    public function ammunitionReleases()
    {
        return $this->hasMany(AmmunitionRelease::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable($query)
    {
        return $query->where('status','Available');
    }

    public function scopeIssued($query)
    {
        return $query->where('status','Issued');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status','Maintenance');
    }

    public function scopeLost($query)
    {
        return $query->where('status','Lost');
    }

    public function scopeRetired($query)
    {
        return $query->where('status','Retired');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDescriptionAttribute()
    {
        return "{$this->make} {$this->model}";
    }

    public function getFullNameAttribute()
    {
        return "{$this->make} {$this->model} ({$this->serial_no})";
    }

    public function getCurrentHolderAttribute()
    {
        return optional(
            $this->currentAssignment
        )->user;
    }

    public function getLicenseExpiryAttribute()
    {
        return optional(
            $this->activeLicense
        )->expiry_date;
    }

    public function getIsLicensedAttribute()
    {
        if (!$this->activeLicense) {
            return false;
        }

        return $this->activeLicense->expiry_date >= today();
    }

    public function getDaysUntilLicenseExpiryAttribute()
    {
        if (!$this->activeLicense) {
            return null;
        }

        return today()->diffInDays(
            $this->activeLicense->expiry_date,
            false
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function issueTo($user, $branch = null)
    {
        return $this->assignments()->create([

            'user_id' => $user->id,

            'branch_id' => optional($branch)->id,

            'issued_at' => now(),

            'issued_by' => auth()->user()->name

        ]);
    }

    public function markAvailable()
    {
        $this->update([
            'status' => 'Available'
        ]);
    }

    public function markIssued()
    {
        $this->update([
            'status' => 'Issued'
        ]);
    }

    public function markMaintenance()
    {
        $this->update([
            'status' => 'Maintenance'
        ]);
    }

    public function retire()
    {
        $this->update([
            'status' => 'Retired'
        ]);
    }

    public function markLost()
    {
        $this->update([
            'status' => 'Lost'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Helpers
    |--------------------------------------------------------------------------
    */

    public static function totalAvailable()
    {
        return static::available()->count();
    }

    public static function totalIssued()
    {
        return static::issued()->count();
    }

    public static function totalMaintenance()
    {
        return static::maintenance()->count();
    }

    public static function totalLost()
    {
        return static::lost()->count();
    }

    public static function totalRetired()
    {
        return static::retired()->count();
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : asset('images/no-image.png');
    }

    public function getStatusBadgeAttribute()
    {
        $colors = [
            'Available' => 'success',
            'Issued' => 'primary',
            'Maintenance' => 'warning',
            'Lost' => 'danger',
            'Retired' => 'secondary',
        ];

        $color = $colors[$this->status] ?? 'dark';

        return "<span class='badge badge-{$color}'>{$this->status}</span>";
    }
}
