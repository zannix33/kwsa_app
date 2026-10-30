<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Branch extends Model {
 use HasFactory;
 protected $fillable=['area_id','company_id','name','address','province','baranggay','operation_start','operation_end','operation_saturday_start','operation_saturday_end','operation_sunday_start','operation_sunday_end'];
 public function client(){ return $this->belongsTo(Client::class); }
 public function area(){ return $this->belongsTo(Area::class); }
 public function company(){ return $this->belongsTo(Company::class); }
 public function users(){ return $this->belongsToMany(User::class)->withTimestamps(); }
 public function dtrs(){ return $this->hasMany(DailyTimeRecord::class); }
}
