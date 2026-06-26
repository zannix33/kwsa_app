<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'type',
        'code',
        'description',
        'quantity',
        'rate',
        'amount',
        'remarks'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

}
