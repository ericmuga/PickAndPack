<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

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
                ->leftJoin('lines', function (JoinClause $join) {
                            $join->on('lines.item_no', '=', 'Transfers.item_no')
                                 ->join('orders','lines.order_no','orders.order_no')
                                 ->where('orders.shp_date','>=',today());
                        })
                ->selectRaw('Transfers.item_no,items.description,SUM(receiver_total_weight) total_weight, SUM(lines.order_qty) as order_qty,SUM(lines.ass_qty) as ass_qty')
                ->groupBy('Transfers.item_no','items.description');


     }




}
