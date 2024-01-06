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
           return $this->query()
                ->dispatch()
                ->received()
                ->finished()
                ->when($date, fn($q)=>$q->where('updated_at','<=',$date))
                ->join('items','items.item_no','Transfers.item_no')
                // ->leftJoinSub($salesSubQuery,'sales',fn(JoinClause $join)=>$join->on('sales.item_no','Transfers.item_no'))
                // ->leftJoinSub($assemblySubQuery,'assembly',fn(JoinClause $join)=>$join->on('assembly.item_no','Transfers.item_no'))
                ->selectRaw('
                             Transfers.item_no,
                             items.description,
                             SUM(receiver_total_weight) as Inventory_Kgs,
                             (select sum(a.order_qty) from lines as a
                             inner join orders as b on a.order_no=b.order_no and b.shp_date>=DATEADD(d,2,DATEDIFF(d,0,GETDATE()))
                             where a.item_no=Transfers.item_no
                             )Due_After_Tomorrow,

                             (select sum(a.order_qty) from lines as a
                             inner join orders as b on a.order_no=b.order_no and b.shp_date>=DATEADD(d,0,DATEDIFF(d,0,GETDATE())) and b.shp_date<=DATEADD(d,2,DATEDIFF(d,0,GETDATE()))
                             where a.item_no=Transfers.item_no
                             )Today_and_Tomorrow,

                             (select sum(line_prepacks.total_quantity)
                                  from line_prepacks
                                  inner join lines as a  on a.line_no=line_prepacks.line_no
                                  inner join orders as b on a.order_no=b.order_no and b.shp_date>=DATEADD(d,0,DATEDIFF(d,0,GETDATE()))
                                  where
                                    line_prepacks.line_no=a.line_no
                                    and line_prepacks.order_no=a.order_no
                                    and a.item_no=Transfers.item_no
                                ) as prepacked_qty,

                              (select sum(assembly_lines.ass_qty)
                                  from assembly_lines
                                  inner join lines as a  on a.line_no=assembly_lines.line_no
                                  inner join orders as b on a.order_no=b.order_no and b.shp_date>=DATEADD(d,0,DATEDIFF(d,0,GETDATE()))
                                  where
                                  assembly_lines.line_no=a.line_no
                                    and assembly_lines.order_no=a.order_no
                                    and a.item_no=Transfers.item_no
                                ) as assembled_qty,
                                (select sum(packing.packed_qty)
                                  from packing
                                  inner join lines as a  on a.line_no=packing.line_no
                                  inner join orders as b on a.order_no=b.order_no and b.shp_date>=DATEADD(d,0,DATEDIFF(d,0,GETDATE()))
                                  where
                                  packing.line_no=a.line_no
                                    and packing.order_no=a.order_no
                                    and a.item_no=Transfers.item_no
                                ) as packed_qty

                           ')
                ->groupBy('Transfers.item_no','items.description');


     }




}
