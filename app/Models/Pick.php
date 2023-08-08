<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pick extends Model
{
    use HasFactory;

    protected $table='PickOrders';


    public function orders()
     {
       //this will return all orders of that pick
       return Order::whereIn('order_no',Pick::select('order_no')->distinct());

        //return $this->belongsToMany(Order::class,'PickOrders','pick_no','order_no','pick_no','order_no');
     }
}
