<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    use HasFactory;

  public function order()
  {
    return $this->belongsTo(Order::class,'order_no','order_no');
  }

//   public function checkPart($order_no,$part_no)
//   {
//     return $this->where('order_no')
//   }

}
