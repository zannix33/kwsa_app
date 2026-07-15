<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'firstname',
        'middlename',
        'lastname',
        'address',
        'city',
        'province',
        'email',
        'phone',
        'religion',
        'spouse_name',
        'spouse_birthdate',
        'beneficiary_name',
        'beneficiary_contact',
        'password',
        'civil_status',
        'birthdate',
        'height',
        'weight',
        'sss',
        'tin',
        'pagibig',
        'philhealth',
        'bloodtype',
        'position_id',
        'lesp_num',
        'lesp_issued',
        'lesp_expiry',
        'date_hired',
        'dt_date',
        'photo',
        'department_type',
        'employee_no',
        'lesp_category',
        'micro_savings_account_no',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date:Y-m-d',
        'spouse_birthdate' => 'datetime:Y-m-d',
        'date_hired' => 'datetime:Y-m-d',
        'dt_date' => 'datetime:Y-m-d',
        'lesp_expiry' => 'datetime:Y-m-d',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {

            $nextId = static::max('id') + 1;

            $user->employee_no = 'KSA-'.str_pad($nextId, 6, '0', STR_PAD_LEFT);

            $user->name = trim(
                $user->firstname.' '.$user->lastname
            );
        });

        static::updating(function ($user) {

            $user->name = trim(
                $user->firstname.' '.$user->lastname
            );
        });
    }

    public function scopeNearAgeRestriction($query, $retirementAge = 55)
    {
        $minBirthdate = Carbon::today()->subYears($retirementAge);
        $maxBirthdate = Carbon::today()->subYears($retirementAge - 2);

        return $query->whereBetween('birthdate', [
            $minBirthdate,
            $maxBirthdate
        ]);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class)
            ->withTimestamps();
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function dailyTimeRecords()
    {
        return $this->hasMany(DailyTimeRecord::class);
    }

    public function getFullNameAttribute()
    {
        return trim(
            $this->firstname . ' ' .
            $this->middlename . ' ' .
            $this->lastname
        );
    }

    //Arms

    public function armAssignments()
    {
        return $this->hasMany(ArmAssignment::class);
    }

    public function currentFirearm()
    {
        return $this->hasOne(ArmAssignment::class)
            ->whereNull('returned_at');
    }

    public function ammunitionReleases()
    {
        return $this->hasMany(AmmunitionRelease::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function getDailyRateAttribute() {
        $rate = 0;

        if($this->branch) {
            $rate = $this->branch->area->rate;
            $rate = PayrollRate::where('slug', ($rate ? $rate : 'ncr'))->first()->rate;
        }

        if($this->area) {
            $rate = $this->area->rate;
            $rate = PayrollRate::where('slug', ($rate ? $rate : 'ncr'))->first()->rate;
        }

         return $rate;
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class)
            ->withTimestamps();
    }
}
