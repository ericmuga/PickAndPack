<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  Awobaz\Compoships\Compoships;

class Packing extends Model
{
    use HasFactory;
    use Compoships;
   protected $table='packing';

   protected $guarded=[];

   public function order()
   {
    return $this->belongsTo(Order::class,'order_no','order_no');
   }

   public function line()
   {
    return $this->belongsTo(Line::class,['order_no','line_no'],['order_no','line_no']);
   }

   public function packing_session()
   {
     return $this->belongsTo(PackingSession::class);
   }
}
