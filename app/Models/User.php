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
        'position',
        'lesp_num',
        'lesp_issued',
        'lesp_expiry',
        'date_hired',
        'dt_date',
        'branch_id',
        'area_id',
        'photo',
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

    public function scopeNearAgeRestriction($query, $retirementAge = 55)
    {
        $minBirthdate = Carbon::today()->subYears($retirementAge);
        $maxBirthdate = Carbon::today()->subYears($retirementAge - 2);

        return $query->whereBetween('birthdate', [
            $minBirthdate,
            $maxBirthdate
        ]);
    }

    /*

    public function branches()
    {
        return $this->belongsToMany(
            Branch::class,
            'branch_user'
        )->withTimestamps();
    }

    */

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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
}
