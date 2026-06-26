<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SssContri extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_salary',
        'to_salary',
        'premium',
        'active',
    ];
}
