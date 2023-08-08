<?php

namespace App\Http\Controllers;

use App\Models\{Confirmation,Order,Item};
use App\Http\Resources\{ConfirmationResource,OrderResource};
use App\Traits\ExcelExportTrait;
use Illuminate\Http\Request;
use App\Services\{SearchQueryService,ExcelExportService};
use Carbon\Carbon;


class ConfirmationController extends Controller
{
    // use ExcelExportTrait;


public function index(Request $request)
{
    // Define the columns to select in the query
    // dd($request->all());
    $columns = ['customer_name', 'shp_name', 'order_no', 'shp_date', 'sp_code', 'ending_date','ended_by'];

    $queryBuilder = Order::current()
                         ->when($request->has('isConfirmed')&&($request->isConfirmed=='true'),fn($q)=>$q->confirmed()) // You can also use `Order::firstWhere('no', 2)` here
                         ->when(!($request->has('isConfirmed'))||($request->has('isConfirmed')&&($request->isConfirmed=='false')),fn($q)=>$q->pending())
                         ->select($columns); // You can also use `Order::firstWhere('no', 2)` here
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
                ->search()
                ->orderByDesc('ending_date')
                ->paginate(15)
                ->withQueryString();


    // Get the list of prepack items
    $prepackItems = Item::select('description', 'item_no')
                        ->whereHas('prepacks', function ($q) {
                                        $q->where('isActive',true);
                                    })
                        ->get();



    // Return the view with data
    // dd($request->all());
    return inertia('Orders/List', [
        'orders' => OrderResource::collection($orders),
        'columnListing' => $columns,
        'items' => $prepackItems,
        'previousInput'=>$request->all(),
    ]);
}

public function export()
{
    $collection =
                 Confirmation::join('orders','orders.order_no','confirmations.order_no')
                             ->select('confirmations.id',
                                     'confirmations.created_at',
                                     'confirmations.user_id',
                                     'confirmations.order_no',
                                     'confirmations.part_no',
                                     'orders.shp_name',
                                     'orders.customer_name',
                                     'sp_code',
                                     'sp_name')
                             -> where('confirmations.created_at', '>=', today())->get()
                             ->map(function ($confirmation) {
                                $confirmation->created_at = Carbon::parse($confirmation->created_at)->toDateTimeString();
                                return $confirmation;
                            });

    $headings = $collection->count() > 0 ? array_keys($collection->first()->toArray()) : [];

    $exportService = new ExcelExportService($collection, collect($headings));
    return $exportService->export('confirmations');
}


    public function store(Request $request)
    {
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Confirmation  $confirmation
     * @return \Illuminate\Http\Response
     */
    public function show(Confirmation $confirmation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Confirmation  $confirmation
     * @return \Illuminate\Http\Response
     */
    public function edit(Confirmation $confirmation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateConfirmationRequest  $request
     * @param  \App\Models\Confirmation  $confirmation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConfirmationRequest $request, Confirmation $confirmation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Confirmation  $confirmation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Confirmation $confirmation)
    {
        //
    }
}
