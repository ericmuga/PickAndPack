<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  Awobaz\Compoships\Compoships;

class AssemblyLine extends Model
{
    use HasFactory;
    use Compoships;


   protected $guarded=['id'];

   public function user()
   {
    return $this->belongsTo(User::class);
   }

   public function order()
   {
    return $this->belongsTo(Order::class,'order_no','order_no');
   }

   public function line()
   {
    return $this->belongsTo(Line::class,['order_no','line_no'],['order_no','line_no']);
   }

   public function session()
   {
    return $this->belongsTo(AssemblySession::class);
   }


}
