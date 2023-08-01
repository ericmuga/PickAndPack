<?php

namespace App\Http\Controllers;

use App\Models\{Confirmation,Order,Item};
use App\Http\Resources\{ConfirmationResource,OrderResource};
use App\Traits\ExcelExportTrait;
use Illuminate\Http\Request;
use App\Services\SearchQueryService;


class ConfirmationController extends Controller
{
    use ExcelExportTrait;


public function index(Request $request)
{
    // Define the columns to select in the query
    $columns = ['customer_name', 'shp_name', 'order_no', 'shp_date', 'sp_code', 'ending_date','ended_by'];

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
    return inertia('Orders/List', [
        'orders' => OrderResource::collection($orders),
        'columnListing' => $columns,
        'items' => $prepackItems,
    ]);
}

    public function export()
    {
        $data = Confirmation::first()->toArray(); // Replace with your actual Model

        $filename = 'data_export_' . date('Ymd_His') . '.xlsx';

        return $this->exportExcel($data, ConfirmationResource::class, $filename);


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
     * @param  \App\Http\Requests\StoreConfirmationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConfirmationRequest $request)
    {
        //
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
