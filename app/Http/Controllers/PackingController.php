<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{PackingOrderResource,LineResource};
use Carbon\Carbon;
use App\Helpers\ColumnListing;
use App\Models\{Line,Order, Packing,PackingSession};
use Illuminate\Support\Facades\DB;
use App\Services\MyServices;
class PackingController extends Controller
{

    public function index(Request $request)
    {
        //this will display orders ready for packing

        $orders= PackingOrderResource::collection(Order::query()
                                                ->when($request->has('search'),fn($q)=>
                                                        $q->where('order_no','like','%'.$request->search)
                                                        )
                                                ->whereHas('assembly_sessions',fn($q)=>$q->where('system_entry',false))
                                                ->shipcurrent()
                                                ->orderByDesc('ending_date')
                                                ->orderByDesc('ending_time')
                                                ->paginate(5)
                                                ->withQuerystring()

                                            );



     return inertia('Packing/List',compact('orders'));



    }


    public function packOrder(Request $request)
    {
        // Get the items that belong to the order and part for packing


        $orderLines = Line::query()
                            ->where('order_no', $request->order_no)
                            ->where('part', $request->part_no)
                            ->withSum('prepacks', 'total_quantity')
                            ->with('packing','order')
                            ->orderBy('item_description')
                            ->paginate(300)
                            ->appends($request->all())
                            ->withQueryString();


        return inertia('Packing/PartPackLines', [
            'orderLines' => LineResource::collection($orderLines),
            'previousInput' => $request->all(),
        ]);
    }

    public function store(Request $request)
    {



       $session=PackingSession::updateOrCreate([
                                       'order_no'=>$request->data[0]['order_no'],
                                       'part'=>Line::where('order_no',$request->data[0]['order_no'])
                                               ->where('line_no',$request->data[0]['line_no'])
                                               ->first()->part,
                                        'user_id'=>$request->user()->id,
                                        'system_entry'=>$request->autosave
                                       ],
                                        [
                                          'packing_time'=>$request->packing_time,

                                        ]

                                    );





    foreach($request->data as $line)
    {

        DB::table('packing')
          ->where('line_no',$line['line_no'])
          ->where('order_no',$line['order_no'])
          ->delete();



        Packing::create([

                        'order_no'=>$line['order_no'],
                        'line_no'=>$line['line_no'],
                        'packing_session_id'=>$session->id,
                        'packed_qty'=>$line['packed_qty'],
                        'packed_pcs'=>MyServices::zeroIfNullOrBlank('packed_pcs',$line,0),
                        'from_vessel'=> MyServices::zeroIfNullOrBlank('from_vessel',$line,0),
                        'to_vessel'=>MyServices::zeroIfNullOrBlank('to_vessel',$line,0)>0?:MyServices::zeroIfNullOrBlank('from_vessel',$line,0),
                        'from_batch'=>MyServices::zeroIfNullOrBlank('from_batch',$line,''),
                        'to_batch'=>MyServices::preventNullsFromArray('to_batch',$line,'')?:MyServices::preventNullsFromArray('from_batch',$line,''),
                        'vessel'=>MyServices::preventNullsFromArray('vessel',$line,'Crate'),

                   ]);

    }


     if (!$request->autosave)
      return redirect(route('packing.index'));
    else
      return response('',200,[]);
}





}
