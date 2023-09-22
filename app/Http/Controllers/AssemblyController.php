<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{OrderResource,LineResource};
use App\Models\{Order,Line,AssemblySession,AssemblyLine};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssemblyController extends Controller
{
    

    //this will show all orders pending assembly

    public function index(Request $request)
    {
        
                $orders= OrderResource::collection(Order::query()
                                                        ->when($request->has('search'),fn($q)=>
                                                                $q->where('order_no','like','%'.$request->search)
                                                               
                                                                )
                                                        ->whereHas('confirmations')
                                                        ->where('shp_date','>=',Carbon::now()->toDateString())
                                                        ->orderByDesc('ending_date')
                                                        ->orderByDesc('ending_time')
                                                        ->with('confirmations')
                                                        ->paginate(5)
                                                        ->withQuerystring()

                                                    );


             return inertia('Orders/Assemble',['orders'=>$orders]);

      }


   // Get the items that belong to the order and part
    public function assembleOrder(Request $request)
    {
        
        $orderLines = Line::query()
                            ->where('order_no', $request->order_no)
                            ->where('part', $request->part_no)
                            ->withSum('prepacks', 'total_quantity')
                            // ->with('order.customer_name,order.shp_name')
                            ->orderBy('item_description')
                            ->paginate(15)
                            ->appends($request->all())
                            ->withQueryString();

        return inertia('Assembly/PartPackLines', [
                            'orderLines' => LineResource::collection($orderLines),
                            'previousInput' => $request->all(),
                        ]);
    }


public function store(Request $request)
 {
    
    //create assembly session

     AssemblySession::create([
                               'order_no'=>$request->data[0]['order_no'],
                               'part'=>Line::where('order_no',$request->data[0]['order_no'])
                                   ->where('line_no',$request->data[0]['line_no'])
                                   ->first()->part,
                                'assembly_time'=>$request->assembly_time,
                                'user_id'=>$request->user()->id
                            ]);

    //create assembly lines
    foreach($request->data as $line)
    {
         // dd($line);

        DB::table('assembly_lines')
          ->where('line_no',$line['line_no'])
          ->where('order_no',$line['order_no'])
          ->delete();

        AssemblyLine::create([
            'order_no'=>$line['order_no'],
            'line_no'=>$line['line_no'],
            'from_batch'=>array_key_exists('from_batch',$line)?$line['from_batch']:'',
            'to_batch'=>array_key_exists('to_batch',$line)?$line['to_batch']:'',
            'user_id'=>$request->user()->id,
            'ass_qty'=>$line['assembled_qty'],
        ]);
         }

       return redirect(route('assembly.index'));
  }







}
