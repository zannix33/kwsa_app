<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AmmunitionRelease extends Model
{
    protected $fillable = [

        'ammunition_id',

        'arm_id',

        'user_id',

        'branch_id',

        'reference_no',

        'purpose',

        'quantity',

        'returned_quantity',

        'consumed_quantity',

        'released_at',

        'released_by',

        'returned_at',

        'received_by',

        'remarks'

    ];

    protected $casts = [

        'released_at' => 'datetime',

        'returned_at' => 'datetime',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function ammunition()
    {
        return $this->belongsTo(Ammunition::class);
    }

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
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($release) {

            if (!$release->released_at) {
                $release->released_at = now();
            }

            if (!$release->released_by && auth()->check()) {
                $release->released_by = auth()->user()->name;
            }

            $release->consumed_quantity = $release->quantity;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDuty($query)
    {
        return $query->where('purpose', 'Duty');
    }

    public function scopeTraining($query)
    {
        return $query->where('purpose', 'Training');
    }

    public function scopeReturned($query)
    {
        return $query->whereNotNull('returned_at');
    }

    public function scopeOutstanding($query)
    {
        return $query->whereNull('returned_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getOutstandingQuantityAttribute()
    {
        return max(
            0,
            $this->quantity - $this->returned_quantity
        );
    }

    public function getConsumptionRateAttribute()
    {
        if ($this->quantity == 0) {
            return 0;
        }

        return round(
            ($this->consumed_quantity / $this->quantity) * 100,
            2
        );
    }

    public function getIsReturnedAttribute()
    {
        return !is_null($this->returned_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Release ammunition.
     */
    public static function release(array $data)
    {
        return DB::transaction(function () use ($data) {

            $ammo = Ammunition::lockForUpdate()
                ->findOrFail($data['ammunition_id']);

            $ammo->issue($data['quantity']);

            return static::create($data);

        });
    }

    /**
     * Return unused ammunition.
     */
    public function returnAmmunition(
        $returnedQuantity,
        $receivedBy = null
    ) {

        DB::transaction(function () use (
            $returnedQuantity,
            $receivedBy
        ) {

            $this->ammunition->receive($returnedQuantity);

            $this->update([

                'returned_quantity' => $returnedQuantity,

                'consumed_quantity' =>
                    $this->quantity - $returnedQuantity,

                'returned_at' => now(),

                'received_by' =>
                    $receivedBy
                    ??
                    optional(auth()->user())->name

            ]);

        });

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Helpers
    |--------------------------------------------------------------------------
    */

    public static function issuedToday()
    {
        return static::whereDate(
            'released_at',
            today()
        )->sum('quantity');
    }

    public static function consumedToday()
    {
        return static::whereDate(
            'released_at',
            today()
        )->sum('consumed_quantity');
    }

    public static function outstandingCount()
    {
        return static::outstanding()->count();
    }

    public static function dutyRounds()
    {
        return static::duty()->sum('consumed_quantity');
    }

    public static function trainingRounds()
    {
        return static::training()->sum('consumed_quantity');
    }

}
