<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'rate',
        'active',
    ];

    protected $casts = [
        'rate'   => 'double',
        'active' => 'boolean',
    ];
}
