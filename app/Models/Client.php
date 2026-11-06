<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'active', 'category', 'age_limit',
        'apply_overtime_rate', 'apply_night_diff_rate',
        'premium_combination_method',
    ];

    protected $casts = [
        'apply_overtime_rate' => 'boolean',
        'apply_night_diff_rate' => 'boolean',
    ];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
