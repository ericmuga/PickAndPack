<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Line extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');
    }

    // public function part()
    // {
    //     return $this->belongsTo(Part::class,'part','part');
    // }

    public function scopeOfPart(Builder $query, $part) :void
    {
        $query->where('part',$part);
    }

    public function prepacks()
    {
       return $this->belongsToMany(Prepack::class,'line_prepack_pivot','line_id','prepack_name');
    }



}
