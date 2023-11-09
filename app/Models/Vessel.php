<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vessel extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    public function loadingSession()
    {
        return $this->belongsTo(LoadingSession::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');
    }


}
