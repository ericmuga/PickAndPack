<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{AssemblyLine, Order,Item, LinePrepack, Part, Pick, Prepack};
use App\Http\Resources\{LinePrepackResource, OrderResource,LineResource, PrepackResource};
use App\Models\Confirmation;
use App\Models\Line;
use Illuminate\Database\QueryException;

use App\Exports\{ConfirmationExport,PrepackExport};
use App\Helpers\ColumnListing;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\{SearchQueryService};


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
            return redirect(route('dashboard'));
        }
        catch(QueryException $e){
            $errorMessage = $e->getMessage();

            return back();
        }

    }


public function index(Request $request, $e = null)
{
    // Define the columns to select in the query
    $columns = ['customer_name', 'shp_name', 'order_no', 'shp_date', 'sp_code', 'ending_date','ended_by'];


    // Add search functionality with efficient search on indexed columns


// Usage example
$queryBuilder = Order::current()
                     ->select($columns);
                    //  ->when($request->has('isConfirmed')&&($request->Confirmed=='true'),fn($q)=>$q->confirmed()) // You can also use `Order::firstWhere('no', 2)` here
                    //  ->when(!($request->has('isConfirmed'))||($request->has('isConfirmed')&&($request->Confirmed=='false')),fn($q)=>$q->pending()); // You can also use `Order::firstWhere('no', 2)` here
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
    if ($request->has('order_no','part_no'))
    {

        if (!Order::checkConfirmation($request->order_no,$request->part_no))
        {
            Confirmation::insert(['order_no'=>$request->order_no,
                                    'part_no'=>$request->part_no,
                                    'user_id'=>$request->user()->name,
                                    'created_at'=>Carbon::now(),
                                    'updated_at'=>Carbon::now()
                                ]);

       }

       $order=Order::firstWhere('order_no',$request->order_no);
    //    if ($order->getParts()==$order->confirmations()->count())
    //    {
    //     $order->confirmed=true;
    //     $order->save();
    //    }
$this->index($request);
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

   // send through the prepacks for selection in the modal
    $prepacks= Prepack::join('items','items.item_no','prepacks.item_no')
                      ->select('prepacks.id',DB::raw("CONCAT(items.description,'|', prepacks.pack_size) as description"))
                      ->where('isActive',true)
                      ->orderBy('items.description')
                      ->get();
//    dd($prepacks);

// Step 1: Separate the query builder for better readability
// Fetch the pack size in advance
$packSize = Prepack::exists() ? Prepack::orderByDesc('pack_size')->select('pack_size')->first()->pack_size : 0;

$query = Line::query()
            ->whereHas('order', function ($q) use ($packSize) {
                                            $q->where('shp_date', '>=', Carbon::today()->toDateString())
                                                ->confirmed();
                                        })
             ->whereHas('prepackItems', function ($q) {
                        $q->where('isActive', true);
                    })
            ->whereDoesntHave('prepacks')
            ->when($packSize > 0, function ($q) use ($packSize) {
                $q->where('order_qty', '>=', $packSize);
            });
    /////////////////////////////////////////////

    $queryBuilder = $query; // You can also use `Order::firstWhere('no', 2)` here
    $searchParameter = $request->has('search')?$request->search:'';
    $searchColumns = ['item_no','order_no','item_description'];
    $strictColumns = [];
    $relatedModels = [
                        'order' => ['customer_name', 'shp_name','sp_name'],
                        // 'relatedModel2' => ['related_column3'],
                     ];

    $searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, $strictColumns, $relatedModels);
    $lines = $searchService
                ->search()
                ->with(['order'])
                ->orderByDesc('order_no');

$orderLines = LineResource::collection($lines->paginate(15)->appends($request->all())->withQueryString());



$sp_codes = DB::table('orders')
              ->where('shp_date', '>=', Carbon::today()->toDateString())
              ->select('sp_code', DB::raw("CONCAT(sp_code,'|', sp_name) as sp_code_and_name"))
              ->where('status', 'Execute')
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
                                    'orders'=>$orders,
                                    'prepacks'=>$prepacks,

                                    ]);
}






public function pack(Request $request)
{
    //this will be the view to select the order
    //give a list of all orders ready for packing
    // dd($request->all());
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
       dd($request->all());
    //insert the line into assembly line
    foreach($request->data as $line)
    {
        //  dd($line);
        // if (!AssemblyLine::where('order_no',$line['order_no'])
        // ->where('line_no',$line['line_no'])
        // ->exists())

        AssemblyLine::updateOrCreate([
            'order_no'=>$line['order_no'],
            'line_no'=>$line['line_no'],
            'user_id'=>$request->user()->id,
            'ass_qty'=>$line['ass_qty'],
        ]);
        // else redirect()->back()->withErrors(['message'=>'line'.$line['line_no'].'of Order'.$line['order_no'].'already exists']);
    }

    return redirect(route('order.pack'));
}




public function confirm($id)
{
    # code...


    return inertia('Orders/ConfirmAssembly');
}

}
