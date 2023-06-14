<?php

namespace App\Models;

use Carbon\Carbon;
// use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Confirmation;


class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'shp_date' => 'date',
        'ending_date' => 'date',
    ];


    // public function parts()
    // {
    //     return $this->hasMany(Part::class,'order_no','order_no');
    // }

    public function lines()
    {
        return $this->hasMany(Line::class,'order_no','order_no');
    }

    public function scopeExecute(Builder $query) :void
    {
      $query->where('status','Execute');

    }

    public function getParts()
    {
        return $this->lines()
                    ->groupBy('part')
                    ->count();
    }

    public function scopeCurrent(Builder $query) :void
    {
    //   $query->where('ending_date','=',Carbon::today()->toDateString());
    //   $query->where('ending_date','=',Carbon::yesterday()->toDateString());
      $query->where('ending_date','=','2023-06-09');
    }

    public function scopeSector(Builder $query,$sector)
    {
         $query->where('sector','=',$sector);
    }

    public function confirmations()
    {
       return $this->hasMany(Confirmation::class,'order_no','order_no');
    }

    public static function checkConfirmation($order_no,$part_no) :bool

    {
        return Confirmation::where('order_no',$order_no)
                        ->where('part_no',$part_no)
                        ->exists();
    }

}
