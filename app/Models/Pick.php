<?php

namespace App\Models;

use Awobaz\Compoships\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pick extends Model
{
    use HasFactory;

     protected $guarded=['id'];

     public function lines()
     {
       return $this->hasMany(Line::class);
     }

     public function user()
     {
        return $this->belongsTo(User::class);
     }

    // protected $table='picks';



//    public function pick_orders()
//    {
//     return $this->hasMany(PickOrder::class,'pick_no','pick_no');
//    }

//     public function orders()
//      {
//        //this will return all orders of that pick
//       return Order::whereIn('order_no',Pick::where('pick_no',$this->pick_no)->select('order_no'));
//         //return $this->belongsToMany(Order::class,'PickOrders','pick_no','order_no','pick_no','order_no');
//      }

//      public function scopeCurrent(Builder $query): void
//      {
//         $query->where('pick_time','>=',today());
//      }




}
