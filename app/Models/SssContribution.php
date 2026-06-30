<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SssContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_salary',
        'to_salary',
        'employee_share',
        'employer_share',
        'ec',
        'rate',
        'active'
    ];
}
