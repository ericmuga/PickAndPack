<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{AssemblyLine, Order,Item, LinePrepack, Part, Pick, Prepack,AsseblySession};
use App\Http\Resources\{LinePrepackResource, OrderResource,LineResource, PrepackResource};
use App\Models\Confirmation;
use App\Models\Line;
use Illuminate\Database\QueryException;
// use App\Http\Controllers\DashboardController;
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


$searchParameter = $request->has('search')?$request->search:'';
$searchColumns = ['customer_name', 'shp_name','order_no'];
$strictColumns = [];
$relatedModels = [
                    'relatedModel1' => ['related_column1', 'related_column2'],
                    'relatedModel2' => ['related_column3'],
                 ];



$searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, [], []);
// dd($searchService);
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
                            ->paginate(5)
                            ->withQueryString()

                            );
    $listing=collect((new ColumnListing('orders'))->getColumns())->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');
    return inertia('Orders/List',['orders'=>$orders,'refreshError'=>null,'columnListing'=>$listing]);

}


public function getOrderPart(Request $request)
{
    // dd('here');
    $order=Order::firstWhere('order_no',$request->order_no)
        //  ->with(['lines'=>fn($q)=>$q->ofPart($request->part)])
         ->get();
    return response()->json(compact('order'));

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

$orderLines = LineResource::collection($lines->paginate(5)->appends($request->all())->withQueryString());



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





public function confirm($id)
{
    # code...


    return inertia('Orders/ConfirmAssembly');
}

}
