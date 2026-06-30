<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'area_id',
        'category',
        'incident_type',
        'incident_date',
        'incident_time',
        'location',
        'description',
        'action_taken',
        'recommendation',
        'status',
        'reported_by',
        'investigated_by'
    ];

    protected $dates = [
        'incident_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class,'reported_by');
    }

    public function investigator()
    {
        return $this->belongsTo(User::class,'investigated_by');
    }

    public function attachments()
    {
        return $this->hasMany(IncidentAttachment::class);
    }
}
