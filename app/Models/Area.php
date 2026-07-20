<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\PayrollRate;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'rate',
    ];

    protected $appends = [
        'payroll_rate',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function getPayrollRateAttribute() {

        $rate = PayrollRate::where('slug', ($this->rate ? $this->rate : 'ncr'))->first()?->rate;

        return $rate;
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }
}
