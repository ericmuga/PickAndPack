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
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use App\Exports\{ConfirmationExport,PrepackExport};
use App\Helpers\ColumnListing;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\{SearchQueryService};
use Mpdf\Tag\Tr;

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

       $orders=Order::current()->select('confirmed')->get();

    //    dd($orders);
        $data=['todays'=>$orders->count(),
               'pending'=>$orders->where('confirmed',true)->count()
    ];
    //return dashboard

    return inertia('Dashboard',$data);

}









public function index(Request $request, $e = null)
{
    // Define the columns to select in the query
    $columns = ['customer_name', 'shp_name', 'order_no', 'shp_date', 'sp_code', 'ending_date','ended_by'];


    // Add search functionality with efficient search on indexed columns


// Usage example
$queryBuilder = Order::current()->select($columns); // You can also use `Order::firstWhere('no', 2)` here
$searchParameter = $request->has('search')?$request->search:'';
$searchColumns = ['customer_name', 'shp_name','order_no'];
$strictColumns = [];
$relatedModels = [
    'relatedModel1' => ['related_column1', 'related_column2'],
    'relatedModel2' => ['related_column3'],
];

$searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, [], []);
$orders = $searchService
    ->with(['confirmations']) // Example of eager loading related models
    ->search();


    // Get the list of prepack items
    $prepackItems = Item::select('description', 'item_no')
                        ->whereHas('prepacks', function ($q) {
                                        $q->where('isActive',true);
                                    })
                        ->get();



    // Return the view with data
    return inertia('Orders/List', [
        'orders' => OrderResource::collection($orders),
        'refreshError' => $e,
        'columnListing' => $columns,
        'items' => $prepackItems,
    ]);
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
       $order=Order::firstWhere('order_no',$request->order_no);
       if ($order->getParts()==$order->confirmations()->count())
       {
        $order->confirmed=true;
        $order->save();
       }

  }
}

public function filter(Request $request)
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
                            ->withQueryString()

                            );
    $listing=collect((new ColumnListing('orders'))->getColumns())->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');
    return inertia('Orders/List',['orders'=>$orders,'refreshError'=>null,'columnListing'=>$listing]);

}




public function assemble(Request $request)
{
    $prepackItems=Item::select('description','item_no')
                      ->whereHas('prepacks',fn($q)=>$q->where('isActive',true))
                      ->get();




// Step 1: Separate the query builder for better readability
$query = Line::query()->with('order')
    ->when(Prepack::exists(), function ($q) {
        $q->where('order_qty', '>=', Prepack::orderByDesc('pack_size')->select('pack_size')->first()->pack_size);
    })
    ->whereHas('order', function ($q) {
        $q->execute()
            ->where('shp_date', '>=', Carbon::today()->toDateString())
            ->confirmed();
    })
    ->when($request->has('search'), function ($q) use ($request) {
        $q->where(function ($q) use ($request) {
            $q->where('item_no', 'like', '%' . $request->search . '%')
                ->orWhere('order_no', 'like', '%' . $request->search . '%')
                ->orWhere('item_description', 'like', '%' . $request->search . '%')
                ->orWhereHas('order', function ($q) use ($request) {
                    $q->where('customer_name', 'like', '%' . $request->search . '%')
                        ->orWhere('shp_name', 'like', '%' . $request->search . '%')
                        ->orWhere('sp_name', 'like', '%' . $request->search . '%');
                });
        });
    })
    ->whereHas('prepackItems', function ($q) {
        $q->where('isActive', true);
    })
    ->whereDoesntHave('prepacks')
    ->orderBy('order_no');

// Step 2: Optimize eager loading to reduce database queries
$orderLines = LineResource::collection($query->paginate(15)->appends($request->all())->withQueryString());



$sp_codes = DB::table('orders')
              ->where('shp_date', '>=', Carbon::today()->toDateString())
              ->select('sp_code', DB::raw("CONCAT(sp_code,'|', sp_name) as sp_code_and_name"))
              ->distinct()
              ->get();


    $orders = Order::query()
                    ->select('order_no', DB::raw("CONCAT(order_no, '|', customer_name, '|', shp_name) as order_customer_ship"))
                    ->whereIn('order_no',$orderLines->pluck('order_no')->toArray())
                    ->confirmed()
                    ->where('shp_date','>=',Carbon::today()->toDateString())
                    ->where('status', 'Execute')
                    ->get();



  return inertia('Orders/PartLines',
                                    ['orderLines'=>$orderLines ,
                                    'previousInput'=>$request->all(),
                                    'items'=>$prepackItems,
                                    'sp_codes'=>$sp_codes,
                                    'orders'=>$orders

                                    ]);
}



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
// dd($request->all());

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
                            ->when($request->has('order_no'),fn($q)=>$q->whereIn('lines.order_no',$request->order_no))
                            ->join('orders', fn($q)=>$q->on('lines.order_no','=','orders.order_no')
                            ->when($request->has('shp_date'),fn($q)=>$q->where('shp_date',$request->shp_date))
                            ->when(!$request->has('shp_date'),fn($q)=>$q->where('shp_date','>=',Carbon::now()->toDateString()))

                            ->when($request->has('sp_code')&& $request->sp_code[0]!='',fn($q)=>$q->whereIn('orders.sp_code',$request->sp_code))
                            ->where('orders.status','Execute')
                            )
                            ->join('prepacks',fn($q)=>$q->on('prepacks.item_no','=','lines.item_no')
                            ->where('isActive',true))
                            ->get()->groupBy(['order_no','part']);
                    // dd($orderedList);

        foreach($orderedList as $order)
        {

            foreach($order as $part)
            {
                //initializing Carton count for the part
                $cartonCount=0;
                foreach ($part as $item)
                {           DB::table('line_prepacks')
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


                            DB::table('line_prepacks')
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
        return redirect(route('linePrepacks.index'));

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
