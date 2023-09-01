<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{OrderResource,LineResource};
use Carbon\Carbon;
use App\Helpers\ColumnListing;
use App\Models\{Line,Order, Packing};

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


     $listing=collect((new ColumnListing('orders'))->getColumns())->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');
     return inertia('Packing/List',['orders'=>$orders,'refreshError'=>null,'columnListing'=>$listing]);



    }


    public function pack(Request $request)
    {
        // Get the items that belong to the order and part for packing
        $orderLines = Line::query()
                            ->where('order_no', $request->order_no)
                            ->where('part', $request->part_no)
                            ->withSum('prepacks', 'total_quantity')
                            ->withSum('packing','packed_qty')
                            ->orderBy('item_description')
                            ->paginate(15)
                            ->appends($request->all())
                            ->withQueryString();

        return inertia('Packing/PartPackLines', [
            'orderLines' => LineResource::collection($orderLines),
            'previousInput' => $request->all(),
        ]);
    }

    public function closeAssembly(Request $request)
{
    //
    //    dd($request->all());
    //insert the line into assembly line
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
                    ->where('order_no',$line['line_no'])
                    ->delete();

        Packing::create([
            'order_no'=>$line['order_no'],
            'line_no'=>$line['line_no'],
            'user_id'=>$request->user()->id,
            'packed_qty'=>$line['packed_qty'],
            // 'carton_no'=>$line['carton_no'],
        ]);
        // else redirect()->back()->withErrors(['message'=>'line'.$line['line_no'].'of Order'.$line['order_no'].'already exists']);
    }

    return redirect(route('packing.index'));
}


}
