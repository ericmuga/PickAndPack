<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class Transfer extends Model
{
    use HasFactory;

    protected $table='Transfers';
    protected $dates=['created_at','updated_at'];


    public function scopeReceived(Builder $query) :void
    {
        $query->where('received_by','<>','');
    }

    public function scopeDispatch(Builder $query) :void
    {
        $query->where('location_code','3535');
    }

    public function scopeFinished(Builder $query) :void{
        $query->whereRaw("LEFT(Transfers.item_no, 1) IN ('J', 'K')");
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_no','item_no');
    }

    public  function stockSummary($date=null)
     {
        return $this->query()
                ->dispatch()
                ->received()
                ->finished()
                ->when($date, fn($q)=>$q->where('updated_at','<=',$date))
                ->join('items','items.item_no','Transfers.item_no')
                ->selectRaw('Transfers.item_no,items.description,SUM(receiver_total_pieces) total_pieces,SUM(receiver_total_weight) total_weight')
                ->groupBy('Transfers.item_no','items.description');


     }


}
