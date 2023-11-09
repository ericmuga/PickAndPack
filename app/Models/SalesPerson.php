<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPerson extends Model
{
    use HasFactory;

   public function LoadingSessions()
   {
    return $this->hasMany (SalesPerson::class,'sp_code','code');
   }

}
