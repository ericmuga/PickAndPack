<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{

    protected $table='stocks';
    use HasFactory;

    // public function item()
    // {
    //     return $this->belongsTo(Item::class,'item_no','item_no');
    // }


}
