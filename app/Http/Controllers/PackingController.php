<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{OrderResource,LineResource};
use Carbon\Carbon;
use App\Helpers\ColumnListing;
use App\Models\{Line,Order, Packing,PackingSession};
use Illuminate\Support\Facades\DB;
class PackingController extends Controller
{

    public function index(Request $request)
    {
        //this will display orders ready for packing

        $orders= OrderResource::collection(Order::query()
                                                ->when($request->has('search'),fn($q)=>
                                                        $q->where('order_no','like','%'.$request->search)
                                                        // ->orWhere('customer_name','like','%'.$request->search.'%')
                                                        )
                                                ->whereHas('confirmations')
                                                ->where('shp_date','>=',Carbon::now()->toDateString())
                                                ->orderByDesc('ending_date')
                                                ->orderByDesc('ending_time')
                                                ->with('confirmations')
                                                ->paginate(5)
                                                ->withQuerystring()

                                            );


     $listing=collect((new ColumnListing('orders'))->getColumns())
                     ->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');

     return inertia('Packing/List',['orders'=>$orders,'refreshError'=>null,'columnListing'=>$listing]);



    }


    public function pack(Request $request)
    {
        // Get the items that belong to the order and part for packing
       

        $orderLines = Line::query()
                            ->where('order_no', $request->order_no)
                            ->where('part', $request->part_no)
                            ->withSum('prepacks', 'total_quantity')
                            ->with('packing')
                            ->orderBy('item_description')
                            ->paginate(300)
                            ->appends($request->all())
                            ->withQueryString();


        return inertia('Packing/PartPackLines', [
            'orderLines' => LineResource::collection($orderLines),
            'previousInput' => $request->all(),
        ]);
    }

    public function closePacking(Request $request)
    {
    


       PackingSession::create([
                                       'order_no'=>$request->data[0]['order_no'],
                                       'part'=>Line::where('order_no',$request->data[0]['order_no'])
                                           ->where('line_no',$request->data[0]['line_no'])
                                           ->first()->part,
                                        'packing_time'=>$request->packing_time,
                                        'user_id'=>$request->user()->id
                            ]);

        



    foreach($request->data as $line)
    {
        
        DB::table('packing')
          ->where('line_no',$line['line_no'])
          ->where('order_no',$line['order_no'])
          ->delete();

       Packing::create([
                            'order_no'=>$line['order_no'],
                            'line_no'=>$line['line_no'],
                            'user_id'=>$request->user()->id,
                            'packed_qty'=>$line['packed_qty'],
                            'packed_pcs'=>array_key_exists('packed_pcs',$line)?$line['packed_pcs']:0,
                            'from_vessel'=> array_key_exists('from_vessel',$line)?$line['from_vessel']:0,
                            'to_vessel'=>array_key_exists('to_vessel',$line)?$line['to_vessel']:(array_key_exists('from_vessel',$line)?$line['from_vessel']:0),
                            'from_batch'=> array_key_exists('from_batch',$line)?$line['from_batch']:0,
                            'to_batch'=>array_key_exists('to_batch',$line)?$line['to_batch']:(array_key_exists('from_batch',$line)?$line['from_batch']:0),
                            'vessel'=>array_key_exists('vessel',$line)?$line['vessel']:'Crate',
                       ]);
        
    }

    return redirect(route('packing.index'));
}


}
