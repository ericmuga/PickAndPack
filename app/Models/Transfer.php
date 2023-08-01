<?php

namespace App\Models;

use Dotenv\Parser\Lines;
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
       $salesSubQuery= Line::whereHas('order',fn($q)=>$q->invoice()->current())
                           ->select('item_no','ass_qty')
                           ->addSelect(['prepacked_qty' => LinePrepack::selectRaw('sum(line_prepacks.total_quantity) as prepacked_qty')
                                                                      ->whereColumn('order_no','lines.order_no')
                                                                      ->whereColumn('line_no','lines.line_no')]);


                    //assembly line is anything under execute with a shipment date later than today

       $assemblySubQuery= Line::whereHas('order',fn($q)=>$q->execute()->shipCurrent())
                              ->select('item_no','ass_qty','order_qty')
                              ->addSelect(['prepacked_qty' => LinePrepack::selectRaw('sum(line_prepacks.total_quantity) as prepacked_qty')
                                                                      ->whereColumn('order_no','lines.order_no')
                                                                      ->whereColumn('line_no','lines.line_no')]);

        return $this->query()
                ->dispatch()
                ->received()
                ->finished()
                ->when($date, fn($q)=>$q->where('updated_at','<=',$date))
                ->join('items','items.item_no','Transfers.item_no')
                ->leftJoinSub($salesSubQuery,'sales',fn(JoinClause $join)=>$join->on('sales.item_no','Transfers.item_no'))
                ->leftJoinSub($assemblySubQuery,'assembly',fn(JoinClause $join)=>$join->on('assembly.item_no','Transfers.item_no'))
                ->selectRaw('Transfers.item_no,
                             items.description,
                             SUM(receiver_total_weight) as Inventory_Kgs,
                             SUM(assembly.order_qty) as ordered_qty,
                             SUM(assembly.ass_qty)+SUM(assembly.prepacked_qty)+SUM(sales.ass_qty)+SUM(sales.prepacked_qty) as assembled_qty'

                           )
                ->groupBy('Transfers.item_no','items.description');


     }




}
