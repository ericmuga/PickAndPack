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




    public function lines()
    {
        return $this->hasMany(Line::class,'order_no','order_no');
    }

    public function scopeExecute(Builder $query) :void
    {
      $query->where('status','Execute');

    }


    public function scopeInvoice(Builder $query) :void
    {
      $query->where('status','Invoice');

    }
    public function getParts()
    {
        return $this->lines()->get()->groupBy('part')->count();

    }


    public function FunctionName($value='')
    {
        // code...
    }

    public function scopeCurrent(Builder $query) :void
    {
      $query->where('ending_date','>=',Carbon::today()->toDateString());
    }

     public function scopeShipCurrent(Builder $query) :void
    {
      $query->where('shp_date','>=',Carbon::today()->toDateString());
    }

     public function scopeConfirmed(Builder $query) :void
     {
        $query->where('confirmed',1);
     }

     public function scopePending(Builder $query): void
     {
        $query->where('confirmed',0);
     }

    public function scopeSector(Builder $query,$sector)
    {
         $query->where('sector','=',$sector);
    }

    public function picks()
    {
        return $this->belongsToMany(Pick::class,'PickOrders','order_no','pick_no','pick_no','order_no');
    }

  
      public function scopeUnAssigned($query)
        {
            $count=$this->getParts();
            return $query->whereHas('assignments', function ($query) use ($count) {
                $query->havingRaw('count(*) < ?', [$count]);
            });
        }

      public function scopeAssigned($query)
        {
            
            return $query->whereHas('assignments', function ($query) use ($count) {
                $query->havingRaw('count(*) = ?', [$count]);
            });
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

    public function linePrepacks()
    {
    //   return $this->hasMany(LinePrepack::class,'order_no','order_no');
    $query = $this->hasMany(LinePrepack::class, 'order_no', 'order_no');
    $query->getQuery()->withoutGlobalScope(OrderByScope::class);
    return $query;
    }

    public function packing_sessions()
    {
        return $this->hasMany(PackingSession::class,'order_no','order_no');
    }

    public function assembly_sessions()
    {
        return $this->hasMany(AssemblySession::class,'order_no','order_no');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class,'order_no','order_no');
    }
}
