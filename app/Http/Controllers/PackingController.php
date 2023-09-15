<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{OrderResource,LineResource};
use Carbon\Carbon;
use App\Helpers\ColumnListing;
use App\Models\{Line,Order, Packing,PackingSession};

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
        // dd('here');
        $orderLines = Line::query()
                            ->where('order_no', $request->order_no)
                            ->where('part', $request->part_no)
                            ->withSum('prepacks', 'total_quantity')
                            ->withSum('packing','packed_qty')
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
    //
       // dd(Line::where('order_no',$request->data[0]['order_no'])
       //                 ->where('line_no',$request->data[0]['line_no'])
       //                 ->first()->part);
    // dd($request->packing_time);
    //insert the line into assembly line
    
    //insert a packing session

// Insert Packing Session
          


       /*

            $table->id();
            $table->string('order_no');
            $table->string('part');
            $table->time('packing_time');
            $table->foreignIdFor(User::class);
            $table->timestamps();

       */
      


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
        //  dd($line);
        // if (!AssemblyLine::where('order_no',$line['order_no'])
        // ->where('line_no',$line['line_no'])
        // ->exists())


       


        if (Packing::where('order_no',$line['order_no'])
                    ->where('order_no',$line['line_no'])
                    ->exists())

        Packing::where('order_no',$line['order_no'])
                    ->where('line_no',$line['line_no'])
                    ->delete();

        Packing::create([
            'order_no'=>$line['order_no'],
            'line_no'=>$line['line_no'],
            'user_id'=>$request->user()->id,
            'packed_qty'=>$line['packed_qty'],
            'packed_pcs'=>$line['packed_pcs'],
            'from_vessel'=>$line['from_vessel'],
            'to_vessel'=>$line['to_vessel'],
            'vessel'=>$line['vessel'],
            ''
            // 'carton_no'=>$line['carton_no'],
        ]);
        // else redirect()->back()->withErrors(['message'=>'line'.$line['line_no'].'of Order'.$line['order_no'].'already exists']);
    }

    return redirect(route('packing.index'));
}


}
