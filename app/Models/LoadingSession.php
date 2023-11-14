<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadingSession extends Model
{
    use HasFactory;

   protected $guarded=['id'];

   public function loader()
   {
     return $this->belongsTo(User::class);
   }

   function loading_assistant()
    {
     return $this->belongsTo(User::class,'loading_assistant_id','id');

   }

   public function vehicle ()
   {
    return $this->belongsTo(Vehicle::class);
   }

   public function driver()
   {
    return $this->belongsTo(User::class,'driver_id','id');
   }

   public function lines()
   {
    return $this->hasMany(LoadingLine::class);
   }


   public function vessels()
   {
    return $this->hasMany(Vessel::class);
   }

   public function SalesPerson()
   {
    return $this->belongsTo(LoadingSession::class,'Code','sp_code');
   }
}
