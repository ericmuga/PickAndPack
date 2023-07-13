<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Awobaz\Compoships\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Staudenmeir\EloquentHasManyDeep\Eloquent\CompositeKey;

use  Awobaz\Compoships\Compoships;
class Line extends Model
{
   use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Compoships;

    use HasFactory;

    protected $primary =['order_no','line_no'];
    public $incrementing=false;

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');
    }

    public function item()
    {
             $this->belongsTo(Item::class,'item_no','item_no');
    }

    public function scopeOfPart(Builder $query, $part) :void
    {
        $query->where('part',$part);
    }

    protected $primaryKey = 'line_no';


    public function prepacks()
    {
            return $this->hasMany(LinePrepack::class,['order_no','line_no'],['order_no','line_no']);
    }

    public function assemblies()
    {
        return $this->hasMany(AssemblyLine::class,['order_no','line_no'],['order_no','line_no']);
    }

   public function prepackItems()
   {
           return $this->hasManyThrough(Prepack::class,Item::class,'item_no','item_no','item_no','item_no');
   }

    // public function prepacks()
    // {
    //     return $this->belongsToMany(Prepack::class, 'line_prepack_pivot', 'line_no', 'prepack_name')
    //                 ->withPivot(['prepack_count', 'total_quantity'])
    //                 ->withTimestamps();
    // }


}
