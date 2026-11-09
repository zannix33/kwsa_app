<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllowance extends Model
{
    protected $fillable = ['user_id', 'allowance_type_id', 'amount', 'effective_from', 'effective_to', 'status'];

    protected $casts = ['amount' => 'decimal:2', 'effective_from' => 'date', 'effective_to' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function allowanceType() { return $this->belongsTo(AllowanceType::class); }
}
