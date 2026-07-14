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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getPayrollRateAttribute() {

        $rate = PayrollRate::where('slug', ($this->rate ? $this->rate : 'ncr'))->first()->rate;

        return $rate;
    }
}
