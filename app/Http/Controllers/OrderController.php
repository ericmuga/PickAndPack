<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{AssemblyLine, Order,Item, LinePrepack, Prepack};
use App\Http\Resources\{LinePrepackResource, OrderResource,LineResource};
use App\Models\Confirmation;
use App\Models\Line;
use stdClass;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use App\Exports\{ConfirmationExport,PrepackExport};
use App\Helpers\ColumnListing;
use Maatwebsite\Excel\Facades\Excel;
class OrderController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response


    */


    public function export(Request $request)
    {
        // return Excel::download(new ConfirmationExport([$request->from,$request->to]), 'confirmations.xlsx');
        return Excel::download(new ConfirmationExport(['2023-06-09','2023-06-09']), 'confirmations.xlsx');
    }

    public function exportPrepacks(Request $request)
    {
        //   dd($request->all());
        return Excel::download(new PrepackExport($request), 'prepacks.xlsx');

    }

    public function refresh(Request $request)
    {

        try
        {
            DB::statement('EXEC dbo.refresh');
            return $this->index($request,null);
        }
        catch(QueryException $e){
            $errorMessage = $e->getMessage();

            return back();
        }

    }

    public function dashboard()
    {
        //get stats to show on dashboard


        $data=['todays'=>Order::current()->count(),
        'pending'=>$this->getPending()
    ];
    //return dashboard

    return inertia('Dashboard',$data);

}

public function getPending()
{
    $orders=Order::current()
    ->execute()
    ->get();
    $pending=0;
    foreach ($orders as $order)
    {
        if ($order->lines()
        ->groupBy('order_no')
        ->groupBy('part')
        ->count()>$order->confirmations()->count()
        )

        $pending++;
    }
    return $pending;

}




public function index(Request $request,$e=null)
{
    //  dd('here');
    $orders=OrderResource::collection(Order::query()

    //    ->where('shp_date','>=',Carbon::today()->toDateString())
    ->when($request->has('search'),fn($q)=>
    $q->where('order_no','like','%'.$request->search)
    ->orWhere('customer_name','like','%'.$request->search.'%')
    )
    ->current()
    ->orderByDesc('ending_date')
    ->orderByDesc('ending_time')
    ->with('confirmations')
    //    ->withCount()
    ->paginate(10)
    ->withQuerystring()

);
//   dd($orders);
$listing=collect((new ColumnListing('orders'))->getColumns())->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');

$prepackItems=Item::select('description','item_no')->whereHas('prepacks',fn($q)=>$q->where('isActive',true))->get();

return inertia('Orders/List',['orders'=>$orders,'refreshError'=>$e,'columnListing'=>$listing ,'items'=>$prepackItems]);
}

public function confirmation(Request $request)
{

    // dd($request->all());
    if ($request->has('order_no','part_no')){

        if (!Order::checkConfirmation($request->order_no,$request->part_no))
        {
            Confirmation::insert(['order_no'=>$request->order_no,
            'part_no'=>$request->part_no,
            'user_id'=>$request->user()->name,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        $this->index($request);
    }

}
}

function filter(Request $request)
{


    $searchParams =$request->all();
    $query = Order::query();

    foreach ($searchParams as $key => $value) {
        if ($value !== null) {
            if (is_array($value))
            {

                foreach($value as $k=>$val)
                {
                    if ($k==='from'){
                        $query->where($key,'>=',$val);

                    }
                    else //meaning to
                    $query->where($key,'<=',$val);
                }

            }
            else
            {
                $query->where($key,'like','%'.$value.'%');
            }
            // Add more `when` clauses for other search parameters
        }
    }

    $orders= OrderResource::collection($query->current()
    ->orderByDesc('ending_date')
    ->orderByDesc('ending_time')
    ->with('confirmations')
    ->paginate(10)
    ->withQuerystring()

);
$listing=collect((new ColumnListing('orders'))->getColumns())->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');
return inertia('Orders/List',['orders'=>$orders,'refreshError'=>null,'columnListing'=>$listing]);

}




public function assemble(Request $request)
{
    $prepackItems=Item::select('description','item_no')->whereHas('prepacks',fn($q)=>$q->where('isActive',true))->get();
    $orderLines=LineResource::collection(Line::query()

    ->when(Prepack::exists(),fn($q)=>$q->where('order_qty','>=',Prepack::orderByDesc('pack_size')->select('pack_size')->first()->pack_size))
    ->whereHas('order',fn($q)=>$q->execute()
    ->where('shp_date','>=',Carbon::today()->toDateString())
    // ->where('shp_date','>=','2023-06-21')
    )
    ->when($request->has('search'),fn($q)=>$q->whereHas('prepackItems',fn($q)=>$q->where('isActive',true))
    ->whereDoesntHave('prepacks')
    ->where(fn($q)=>$q->where('item_no','like','%'.$request->search.'%')
    ->orWhere('order_no','like','%'.$request->search.'%')
    ->orWhere('item_description','like','%'.$request->search.'%')
    ->orWhereHas('order',fn($q)=>$q->where('customer_name','like','%'.$request->search.'%')
    ->orWhere('shp_name','like','%'.$request->search.'%')
    ->orWhere('sp_name','like','%'.$request->search.'%')

    ))
    )

    ->whereHas('prepackItems',fn($q)=>$q->where('isActive',true))
    ->whereDoesntHave('prepacks')
    ->with('order')
    ->orderBy('order_no')
    ->paginate(15)
    ->appends([$request->all()])
    ->withQueryString()
);
// dd($orderLines->first());
$sp_codes=Order::whereIn('order_no',$orderLines->pluck('order_no')->toArray())
               ->distinct()
               ->get(['sp_code','sp_name']);
            //    dd($sp_codes);


return inertia('Orders/PartLines',
['orderLines'=>$orderLines ,
'previousInput'=>$request->all(),
'items'=>$prepackItems,
'sp_codes'=>$sp_codes

]);
}

/**
* Show the form for creating a new resource.
*
* @return \Illuminate\Http\Response
*/

public function pack(Request $request)
{
    //this will be the view to select the order
    //give a list of all orders ready for packing
    $orders= OrderResource::collection(Order::query()
    ->when($request->has('search'),fn($q)=>
    $q->where('order_no','like','%'.$request->search)
    // ->orWhere('customer_name','like','%'.$request->search.'%')
    )
    ->where('shp_date','>=',Carbon::now()->toDateString())
    ->orderByDesc('ending_date')
    ->orderByDesc('ending_time')
    ->with('confirmations')
    ->paginate(10)
    ->withQuerystring()

);
$listing=collect((new ColumnListing('orders'))->getColumns())->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');
return inertia('Orders/Pack',['orders'=>$orders,'refreshError'=>null,'columnListing'=>$listing]);



}

//  public function scanItems(Request $request)
//  {
    //     //get the items that belong to that order and part, send them back to vue
    //     $orderLines=LineResource::collection(Line::query()
    //                                             ->where('order_no',$request->order_no)
    //                                             ->where('part',$request->part_no)
    //                                             ->withSum('prepacks','total_quantity')
    //                                             ->orderBy('item_description')
    //                                             ->paginate(15)
    //                                             ->appends([$request->all()])
    //                                             ->withQueryString()
    //                                         );

    //                                 // dd( $orderLines);        //  $previousInput=$request->all();


    //    return inertia('Orders/PartPackLines', ['orderLines'=>$orderLines ,
    //                                           'previousInput'=>$request->all(),

    //                                          ]);
    // }

    public function scanItems(Request $request)
    {
        // Get the items that belong to the order and part
        $orderLines = Line::query()
        ->where('order_no', $request->order_no)
        ->where('part', $request->part_no)
        ->withSum('prepacks', 'total_quantity')
        ->orderBy('item_description')
        ->paginate(15)
        ->appends($request->all())
        ->withQueryString();

        return inertia('Orders/PartPackLines', [
            'orderLines' => LineResource::collection($orderLines),
            'previousInput' => $request->all(),
        ]);
    }




    public function prepack(Request $request)
    {
        //group by order, then by part, then by item

        //  dd($request->all());

        /**
        * get all the lines that are in the filter and have preOrderpacks.
        * for each order
        *     for each part,
        *
        */


        $orderedList=   DB::table('lines')
        ->select('lines.item_no',
        'lines.order_no',
        'lines.order_qty',
        'lines.ass_qty',
        'lines.part',
        'lines.customer_spec',
        'lines.item_description',
        'lines.line_no'
        )
        ->where('ass_qty',0)


        ->when($request->has('item')&&$request->item[0]!='',fn($q)=>$q->whereIn('lines.item_no',$request->item))
        ->when($request->has('order_no'),fn($q)=>$q->where('lines.order_no',$request->order_no))
        ->join('orders', fn($q)=>$q->on('lines.order_no','=','orders.order_no')
        ->when($request->has('shp_date'),fn($q)=>$q->where('shp_date',$request->shp_date))
        ->when(!$request->has('shp_date'),fn($q)=>$q->where('shp_date','>=',Carbon::now()->toDateString()))

        ->when($request->has('sp_code')&& $request->sp_code[0]!='',fn($q)=>$q->whereIn('orders.sp_code',$request->sp_code))
        ->where('orders.status','Execute')
        )
        ->join('prepacks',fn($q)=>$q->on('prepacks.item_no','=','lines.item_no')
        ->where('isActive',true))
        ->get()->groupBy(['order_no','part']);

        foreach($orderedList as $order)
        {

            foreach($order as $part)
            {
                //drop all prepacks for the order



                //initializing Carton count for the part
                $cartonCount=0;
                foreach ($part as $item)
                {           DB::table('line_prepack_pivot')
                    ->where('order_no',$item->order_no)
                    ->delete();
                    // dd($item);
                    //do the prepack here
                    $itemPrepacks=DB::table('prepacks')
                    ->where('item_no',$item->item_no)
                    ->orderByDesc('pack_size')
                    ->get();

                    $remaining=$item->order_qty;
                    $batch_no=preg_replace('/[:\- ]/','', Carbon::now()->toDateTimeString());
                    $carton_no=0;
                    foreach($itemPrepacks as $prepack )
                    {
                        if ($remaining<$prepack->pack_size)
                        continue;
                        else
                        {

                            //get maximum multiples
                            $prepack_count=intdiv($remaining,$prepack->pack_size);
                            $total_qty=$prepack_count*$prepack->pack_size;


                            DB::table('line_prepack_pivot')
                            ->insert([
                                'order_no'=>$item->order_no,
                                'line_no'=>$item->line_no,
                                'prepack_name'=>$prepack->prepack_name,
                                'prepack_count'=>$prepack_count,
                                'total_quantity'=>$total_qty,
                                'user_id'=>$request->user()->id,
                                'batch_no'=>$batch_no,
                                'carton_no'=>'1-'.strval($prepack_count),
                                'created_at'=>Carbon::now(),
                                'updated_at'=>Carbon::now()

                            ]);
                            //update remaining

                            $remaining-=$total_qty;

                        }
                    }






                }
            }
        }


        //return inertia('Prepacks/OrderPrepacks',['prepackLines'=>LinePrepackResource::collection(LinePrepack::paginate(15))]);
        //    $this->orderPrepacks($request);
        return redirect(route('orders.prepacks'));

    }


    public function orderPrepacks(Request $request)
    {
        $prepackItems=Item::select('item_no','description')
        ->whereHas('prepacks',fn($q)=>$q->where('isActive',true))
        ->get();
        return inertia('Prepacks/OrderPrepacks',[
            'prepackLines'=>LinePrepackResource::collection(LinePrepack::with('line','order','user')
            ->paginate(15)
            ->withQueryString()
        ),
        'prepackItems'=>$prepackItems,
        'previousInput'=>$request->all()

    ]);
}


public function closeAssembly(Request $request)
{
    //
    //    dd($request->all());
    //insert the line into assembly line
    foreach($request->extractedData as $line)
    {
        //  dd($line);
        if (!AssemblyLine::where('order_no',$line['order_no'])
        ->where('line_no',$line['line_no'])
        ->exists())

        AssemblyLine::create([
            'order_no'=>$line['order_no'],
            'line_no'=>$line['line_no'],
            'user_id'=>$request->user()->id,
            'ass_qty'=>$line['ass_qty'],
        ]);
        else redirect()->back()->withErrors(['message'=>'line'.$line['line_no'].'of Order'.$line['order_no'].'already exists']);
    }

    return redirect(route('order.pack'));
}




public function confirm($id)
{
    # code...


    return inertia('Orders/ConfirmAssembly');
}

}
