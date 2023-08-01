<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Models\{Transfer,Order};
use Illuminate\Http\Request;
use App\Services\SearchQueryService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * This controller will manage the dashboard activities and view
     *
     * 1. Load Stock Position
     * 2. Show Current confirmations position
     * 3. Show Order Processing Postion
     */

     public static function dashboard(Request $request)
     {


        $queryBuilder = (new Transfer())->stockSummary(); // You can also use `Order::firstWhere('no', 2)` here
        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['customer_name', 'shp_name','order_no'];
        $strictColumns = [];
        $relatedModels = [
            'item' => ['description'],
            // 'relatedModel2' => ['related_column3'],
        ];

        $searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, [], $relatedModels);

        $stocks = $searchService
            // ->with(['item.description']) // Example of eager loading related models
            ->search()
            ->orderByDesc('total_weight')
            ->paginate(15)
            ->withQueryString();



            $orders=Order::current()->select('confirmed')->get();



                $data=[
                       'todays'=>$orders->count(),
                       'pending'=>$orders->where('confirmed',false)->count(),
                       'stocks'=>$stocks,
                       'headers'=>array_keys($stocks->first()->toArray())
                      ];
         return  inertia('Dashboard',$data);

    }



}
