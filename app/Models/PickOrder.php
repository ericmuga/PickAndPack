<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickOrder extends Model
{
    use HasFactory;

    protected $table ='PickOrders';

    public function picks()
    {
        return $this->belongsTo(Pick::class,'pick_no','pick_no');
    }

    public function orders()
    {
     return $this->belongsTo(Order::class,'order_no','order_no');
    }
}
