<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [

        'incident_id',
        'original_name',
        'file_name',
        'mime_type',
        'file_size'

    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }
}
