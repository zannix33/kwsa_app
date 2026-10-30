<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Company extends Model {
 use HasFactory;
 protected $fillable=['client_id','name','active'];
 public function client(){ return $this->belongsTo(Client::class); }
 public function branches(){ return $this->hasMany(Branch::class); }
}
