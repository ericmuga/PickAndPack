<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssemblySession extends Model
{
    use HasFactory;

   protected $guarded =['id'];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');
    }

   public function lines()
   {
    return $this->hasMany(AssemblyLine::class,null,'session_id');
   }

   public function assignment()
   {
    return $this->belongsTo(Assignment::class);
   }

}
