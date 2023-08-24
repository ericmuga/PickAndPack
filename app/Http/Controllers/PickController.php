<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\{Pick, Order, Line, LinePrepack, PickOrder,AssemblyLine};
use App\Http\Resources\{LineResource};
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\ToArray;

class PickController extends Controller
{

    public function index(Request $request)

    {

        //display all picks for the day

        $orders=Order::shipCurrent()->select('order_no')->get();

        $picks = Pick::select('pick_no', 'part')
            //    ->current()
                    ->when(
                        $request->has('search') && $request->search != '',
                        fn ($q) => $q->where('pick_no', 'LIKE', '%' . $request->search)
                            ->orWhereHas('pick_orders', fn ($q) => $q->where('serial_no', 'LIKE', '%' . $request->search)

                            )
                    )


            ->paginate(15);


        $previous = ($request->has('search')) ? $request->search : '';


        return inertia('Picks/List', compact('picks', 'previous'));
    }

    public function show(Request $request)
    {
        // this will show a pick with its orders lines
           $orders=PickOrder::select('order_no')->where('pick_no',$request->pick)->get();
          // $sp=Order::select('sp_code','sp_name')
            $orderLines = DB::table('lines')
                            ->selectRaw('lines.item_description,
                                         lines.item_no,lines.barcode ,
                                         sum(lines.order_qty) as total_order_qty,
                                         sum(line_prepacks.total_quantity) as prepacked_qty,
                                         orders.sp_code,
                                         orders.sp_name,
                                         orders.route_code,
                                         orders.sector
                                         ')
                            ->leftJoin('line_prepacks',fn($q)=>$q->on('line_prepacks.line_no','lines.line_no')->on('line_prepacks.order_no','lines.order_no'))
                            ->join('orders','orders.order_no','lines.order_no')
                            ->whereIn('lines.order_no',$orders)
                            ->where('part',substr($request->pick,2,1))
                            ->groupBy('lines.item_description','lines.barcode','lines.item_no','orders.sp_code',
                                         'orders.sp_name',
                                         'orders.route_code',
                                         'orders.sector')
                            ->get();

        $orderLines = $orderLines->map(function ($item) {
            $item->prepacked_qty = $item->prepacked_qty ?? 0;
            $item->total_order_qty = strval($item->total_order_qty) ?? 0;

            return $item;
        });

        // dd($orderLines);

 return inertia('Picks/PartPackLines', [
                    'orderLines' => $orderLines,
                    'pick_no'=>$request->pick,
                ]);


    }

    private function getPrepacks($orderNos,$line)
    {
      LinePrepack::whereIn('order_no',$orderNos)->where('item_no')
                 ->join('line',fn($q)=>$q->on('line_no',$line->line_no)->on('order_no','order_no'))
                 ->sum('total_quantity');

    }

    public function store(Request $request)
    {
    $selectedOrders=DB::table('PickOrders')
                        ->select('order_no')
                        ->where('pick_no',$request->pick_no)->get()->pluck('order_no');
    $lines= DB::table('lines')
                ->select('order_no','item_no','line_no','order_qty')
                ->whereIn('order_no',$selectedOrders)
                ->where('part',substr($request->pick_no,2,1))
                ->get();
                // dd($lines->pluck('item_no'));
                // dd(substr($request->pick_no,2,1));
     foreach($request->data as $item)
      {

          $itemLines=collect([]);

            foreach($lines as $l)
            {
               if ($l->item_no==$item['item_no'])
                $itemLines->push($l);

            };

           $counter=floatval($item['assembled_qty']);
            foreach($itemLines as $line)
            {

                if($line->order_qty-$counter>0)
                {
                    AssemblyLine::updateOrCreate([
                                                    'order_no'=>$line->order_no,
                                                    'line_no'=>$line->line_no,
                                                    'user_id'=>$request->user()->id,
                                                    'ass_qty'=>($line->order_qty<$counter)?$counter:$line->order_qty
                                                ]);
                    $counter-=$line->order_qty;
                }

            }
     }
      return redirect(route('picks.index'));
    }


}
