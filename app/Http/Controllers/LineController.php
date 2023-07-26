<?php

namespace App\Http\Controllers;

use App\Models\{Line,Item,Prepack,Order,LinePrepack};
use App\Http\Requests\StoreLineRequest;
use App\Http\Requests\UpdateLineRequest;
use App\Http\Resources\LineResource;
use Illuminate\Http\Request;
use App\Services\SearchService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class LineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function add(Request $request)
    {
        dd($request->all());
    }




     public function index(Request $request)
    {

         // Load all the lines with their prepack and assmblies

         $lines=LineResource::collection((new SearchService(new Line()))
         ->with(['prepacks','assemblies'])
         ->search($request));

         return inertia('Line\List',compact('lines'));

    }




public function filtered(Request $request)
{
    $prepackItems=Item::select('description','item_no')
                      ->whereHas('prepacks',fn($q)=>$q->where('isActive',true))
                      ->get();


      $packSize = Prepack::exists() ? Prepack::orderByDesc('pack_size')->select('pack_size')->first()->pack_size : 0;

$query = Line::query()->with('order')
                      ->whereHas('order', function ($q) use ($request) {
                          $q->where('shp_date', '>=', Carbon::today()->toDateString())
                          ->whereIn('sp_code',$request->sp_codes)
                              ->confirmed();
                      })
                      ->whereHas('prepackItems', function ($q) {
                                    $q->where('isActive', true);
                                })
                        ->whereDoesntHave('prepacks')
                        ->orderBy('order_no')
                        ->when($packSize > 0, function ($q) use ($packSize) {
                            $q->where('order_qty', '>=', $packSize);
                        });


// Step 2: Optimize eager loading to reduce database queries
$orderLines = LineResource::collection($query->paginate(15)->appends($request->all())->withQueryString());



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
                                    'orders'=>$orders

                                    ]);
}



    public function prepack(Request $request)

    {
        //prepack all the lines
        /**
         *
         * for each line get the prepackable quantity,
         */

        //  Lines::query()
        //       ->when($request->has('sales_person'));




    }

    public function history (Request $request)
    {

        // dd('here');
            $query = Line::query()->with('order')
                                  ->withSum('assemblies','ass_qty')
                                  ->withSum('prepacks','total_quantity')


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

            ->orderBy('order_no');

            $orderLines = LineResource::collection($query->paginate(15)->appends($request->all())->withQueryString());


            return inertia('Line/History',compact('orderLines'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLineRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLineRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Line  $line
     * @return \Illuminate\Http\Response
     */
    public function show(Line $line)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Line  $line
     * @return \Illuminate\Http\Response
     */
    public function edit(Line $line)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLineRequest  $request
     * @param  \App\Models\Line  $line
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLineRequest $request, Line $line)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Line  $line
     * @return \Illuminate\Http\Response
     */
    public function destroy(Line $line)
    {
        //
    }
}
