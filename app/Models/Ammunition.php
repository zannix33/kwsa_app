<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Ammunition extends Model
{
    protected $fillable = [

        'batch_no',
        'lot_no',

        'caliber',
        'manufacturer',
        'brand',

        'bullet_type',
        'grain',

        'quantity',

        'minimum_stock',

        'unit_cost',

        'received_date',
        'expiry_date',

        'branch_id',

        'supplier',

        'status',

        'remarks'

    ];

    protected $casts = [

        'received_date' => 'date',

        'expiry_date' => 'date',

        'unit_cost' => 'decimal:2',

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

    public function releases()
    {
        return $this->hasMany(AmmunitionRelease::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($ammo) {

            $ammo->syncStatus();

        });

        static::updating(function ($ammo) {

            $ammo->syncStatus();

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable($query)
    {
        return $query->where('status', 'Available');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn(
            'quantity',
            '<=',
            'minimum_stock'
        );
    }

    public function scopeExpired($query)
    {
        return $query->whereDate(
            'expiry_date',
            '<',
            today()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getInventoryValueAttribute()
    {
        return $this->quantity * $this->unit_cost;
    }

    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->minimum_stock;
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return today()->gt($this->expiry_date);
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->caliber} {$this->brand} Batch {$this->batch_no}";
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Receive stock.
     */
    public function receive($quantity)
    {
        $this->increment('quantity', $quantity);

        $this->refresh();

        $this->syncStatus();

        $this->save();

        return $this;
    }

    /**
     * Issue stock.
     */
    public function issue($quantity)
    {
        if ($quantity > $this->quantity) {

            throw ValidationException::withMessages([

                'quantity' => 'Insufficient ammunition stock.'

            ]);

        }

        $this->decrement('quantity', $quantity);

        $this->refresh();

        $this->syncStatus();

        $this->save();

        return $this;
    }

    /**
     * Manual stock adjustment.
     */
    public function adjustStock($quantity)
    {
        $this->update([
            'quantity' => $quantity
        ]);

        $this->syncStatus();

        $this->save();

        return $this;
    }

    /**
     * Synchronize inventory status.
     */
    public function syncStatus()
    {
        if ($this->is_expired) {

            $this->status = 'Expired';

            return;
        }

        if ($this->quantity <= 0) {

            $this->status = 'Consumed';

            return;
        }

        $this->status = 'Available';
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Helpers
    |--------------------------------------------------------------------------
    */

    public static function totalStock()
    {
        return static::sum('quantity');
    }

    public static function totalInventoryValue()
    {
        return static::all()->sum(function ($ammo) {
            return $ammo->inventory_value;
        });
    }

    public static function lowStockCount()
    {
        return static::lowStock()->count();
    }

    public static function expiredCount()
    {
        return static::expired()->count();
    }
}
