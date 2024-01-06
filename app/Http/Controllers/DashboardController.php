<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Models\{AssemblyLine, Line, Transfer,Order, PackingSessionLine, Stock};
use Illuminate\Http\Request;
use App\Services\SearchQueryService;
// use Inertia\Inertia;
// use PhpParser\Node\Expr\Cast\Array_;

class DashboardController extends Controller
{


    public static function getSectorTonnage($confirmed=false)
     {
        $codeMap = [
                        'HORECA' => ['052', '053', '054', '055', '056', '057','061', '001', '002', 'B010', 'B016', 'B025', 'B026'],
                        'RETAIL' => ['012', '013', '023', '024', '031', '032', '034', '062', '129', '611'],
                        'MSA'=> ['041','042','043','044','B285'],
                        'UPC'=>['085','067','180','134','035','608','575','590','037','143'],
                        'F-FOOD'=>['145','151','140','138','141','128','164','147','104','124','154'],
                        // Add other groups as needed
                    ];

         $ordersWithSum = Order::select('sp_code', 'order_no')
                                ->current()
                                ->when($confirmed,fn($q)=>$q->confirmed())
                                ->groupBy('sp_code', 'order_no')
                                ->withSum('lines', 'qty_base')
                                ->get();

        // Group the results by sp_code
        $groupedBySpCode = $ordersWithSum->groupBy('sp_code');

        // Calculate the sum of lines_sum_qty_base for each sp_code
        $sumBySpCode = $groupedBySpCode->map(function ($orders) {
            return $orders->sum('lines_sum_qty_base');
        });


        $sectorTonnage = [];

            foreach ($codeMap as $groupName => $groupCodes) {
                $sectorTonnage[$groupName] = round(collect($sumBySpCode)->only($groupCodes)->sum()/1000,2);
            }

           arsort($sectorTonnage);

           return($sectorTonnage);

     }





     public static function dashboard(Request $request)
     {


         $tonnage=round(Line::whereHas('order',fn($q)=>$q->current())->sum('qty_base')/1000,2);

         $assembled=round(AssemblyLine::whereHas('order',fn($q)=>$q->current())->sum('ass_qty')/1000,2);
         $packed=round(PackingSessionLine::whereHas('order',fn($q)=>$q->current())->sum('weight')/1000,2);
         $loaded=round(PackingSessionLine::whereHas('order',fn($q)=>$q->current())->sum('weight')/1000,2);




        $queryBuilder = (new Transfer())->stockSummary(); // You can also use `Order::firstWhere('no', 2)` here
        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['Transfers.item_no'];
        $strictColumns = [];
        $relatedModels = [
                                'item' => ['description'],

                         ];

        $searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, [], $relatedModels);

        $stocks = $searchService->search()
                                ->orderByDesc('Today_and_Tomorrow')
                                ->paginate(15)
                                ->withQueryString();





            $orders=Order::current()->select('confirmed')->get();


        //   dd($stocks->take(5)->pluck('Today_and_Tomorrow'));


                $data=[
                        'tonnage'=>$tonnage,
                        'sectorTonnage'=>\App\Http\Controllers\DashboardController::getSectorTonnage(),
                        'assembled'=>$assembled,
                        'packed'=>$packed,
                        'loaded'=>$loaded,
                        'todays'=>$orders->count(),
                        'pending'=>$orders->where('confirmed',false)->count(),
                        'stocks'=>$stocks,
                        'top5Labels'=>$stocks->take(5)->pluck('description'),
                        'top5Weights'=>$stocks->take(5)->pluck('Today_and_Tomorrow'),
                        'headers'=>$stocks->count()>0?array_keys($stocks->first()->toArray()):[],
                        // $headings = $collection->count() > 0 ? array_keys($collection->first()->toArray()) : [];
                      ];
         return  inertia('Dashboard',$data);

    }



}
