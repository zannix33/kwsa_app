<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'name',
        'address',
        'province',
        'baranggay',
        'operation_start',
        'operation_end',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /*
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'branch_user'
        )->withTimestamps();
    }
    */

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
