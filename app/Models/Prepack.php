<?php

namespace App\Models;

use Awobaz\Compoships\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use  Awobaz\Compoships\Compoships;
class Prepack extends Model
{
    use HasFactory;
    //  use Compoships;


    protected $fillable=['prepack_name','pack_size','item_no','isActive'];


    protected $primaryKey = 'prepack_name';



     public function linePrepacks()
     {
        return $this->hasMany(LinePrepack::class,'prepack_name','prepack_name');
     }

    //  public function scopeIsActive(Builder $query)
    //  {
    //     $query->where('isActive',true);
    //  }


    public function item()
    {
        return $this->belongsTo(Item::class,'item_no','item_no');
    }

    // public function scopeActive(Builder $query)
    // {
    //     $query->where('isActive',true);
    // }

}
