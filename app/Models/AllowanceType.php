<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceType extends Model
{
    protected $fillable = ['name', 'description', 'default_amount', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'default_amount' => 'decimal:2'];

    public function employeeAllowances()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }
}
