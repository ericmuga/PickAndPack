<?php

namespace App\Models;
use  Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

use App\Models\Scopes\OrderByScope;
class LinePrepack extends Pivot
{
    use HasFactory;
    use Compoships;

    protected $table='line_prepack_pivot';

    protected $primary_key =['order_no','line_no','prepack_name'];


    protected static function booted()
    {
        static::addGlobalScope(new OrderByScope());
    }

    public function line()
    {
      return $this->belongsTo(Line::class,['order_no','line_no'],['order_no','line_no']);

    }

    public function prepack()
    {
        return $this->belongsTo(Prepack::class, 'prepack_name','prepack_name');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');

    }

     public function user()
     {
        return $this->belongsTo(User::class,'user_id','id');
        // return User::find($this->user_id);
     }

    public function item($property)
    {
        // return $this->belongsTo(Item::class,'item_no','item_no');
          return Item::select($property)->where('item_no',Line::select($property)
                                                              ->where('order_no',$this->order_no)
                                                              ->where('line_no',$this->line_no)
                                                              ->first()->{$property}
                                                    )->first();
    }



}
