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

   protected $fillable=['line_no','order_no','user_id','packed_qty','packed_pcs','from_batch','to_batch','from_vessel','to_vessel','vessel'];

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
}
