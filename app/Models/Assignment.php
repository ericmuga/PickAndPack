<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
   protected $guarded =['id'];

   public function assignor()
   {
    return $this->belongsTo(User::class,'assignor_id','id');
   }
   public function assignees()
   {
    return $this->hasMany(User::class,'assignee_id','id');
   }
   public function order()
   {
       return $this->belongsTo(Order::class,'order_no','order_no');
   }

   public function scopeOfPart(Builder $query,$part)
   {
       $query->where('part',$part);
   }
}
