<?php

namespace App\Http\Controllers;


use App\Models\{Item, Transfer,Order, Stock};
use Illuminate\Http\Request;
use App\Services\SearchQueryService;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public static function index(Request $request)
    {


     $queryBuilder = (new Stock())->orderByDesc('closing_weight'); // You can also use `Order::firstWhere('no', 2)` here
       $searchParameter = $request->has('search')?$request->search:'';
       $searchColumns = ['item_no','description'];
       $strictColumns = [];
       $relatedModels = [
                            // 'item' => ['description'],
                            // 'relatedModel2' => ['related_column3'],
                        ];

       $searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, [], $relatedModels);

       $stocks = $searchService->search()
                               // ->orderByDesc('Inventory_Kgs')
                               ->paginate(15)
                               ->withQueryString();



     $items=Item::select('item_no','description')->get();
    //  $chillers=Transfer::select('chiller_code')->distinct();


           $orders=Order::current()->select('confirmed')->get();


       //   dd($stocks->take(5)->pluck('description'));

               $data=[
                      'todays'=>$orders->count(),
                      'pending'=>$orders->where('confirmed',false)->count(),
                      'stocks'=>$stocks,
                       'top5Labels'=>$stocks->take(5)->pluck('description'),
                       'top5Weights'=>$stocks->take(5)->pluck('closing_pieces'),
                       'headers'=>$stocks->count()>0?array_keys($stocks->first()->toArray()):[],
                       'items'=>$items,
                       'chillers'=>[
                                    ['chiller_code'=>'U'],
                                    ['chiller_code'=>'V']
                                  ],
                       // $headings = $collection->count() > 0 ? array_keys($collection->first()->toArray()) : [];
                     ];
        return  inertia('Stocks/Dashboard',$data);

   }


   public function store(Request $request)
   {
    // dd($request->all());

    $stock=DB::table('stock_entries')
            ->where('stock_date',$request->stock_date)
            ->where('item_no',$request->item_no)
            ->where('location',$request->location)
            ->where('chiller',$request->chiller)
            ->get();
     if($stock->count()>0) $stock->delete();

     DB::table('stock_entries')
          ->insert($request->all());


    return redirect(route('stocks.index'));
   }

}
