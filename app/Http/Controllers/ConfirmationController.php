<?php

namespace App\Http\Controllers;

use App\Models\{Confirmation,Order,Item};
use App\Http\Resources\{ConfirmationResource,OrderResource};
use App\Traits\ExcelExportTrait;
use Illuminate\Http\Request;
use App\Services\{SearchQueryService,ExcelExportService};
use Carbon\Carbon;
use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\DB;

class ConfirmationController extends Controller
{
    // use ExcelExportTrait;


public function index(Request $request)
{
    // Define the columns to select in the query
    // dd($request->all());


    $records=$request->records?:5;

     $orders=DB::table('pending_confirmation')
               ->select('order_no','shp_date','sp_code','shp_name','A','B','C','D')
               ->where('shp_date','>=',Carbon::today()->toDateString())
               ->get();

     $confirmations=DB::table('confirmations')
                     ->whereIn('order_no',$orders->pluck('order_no'))
                     ->get();



    return inertia('Orders/List', [
        'orders' => $orders,
        'confirmations'=>$confirmations,
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
             $confirmation=Confirmation::updateOrCreate(['order_no'=>$request->order_no,
                                            'part_no'=>$request->part_no,
                                        ],
                                        ['order_no'=>$request->order_no,
                                            'part_no'=>$request->part_no,
                                            'user_id'=>$request->user()->name,
                                          ]

                                 );

            $confirmedParts=DB::table('confirmations')
                             ->where('order_no',$request->order_no)
                             ->count();

            $parts=DB::table('order_parts')
                     ->where('order_no',$request->order_no)
                     ->count();
            $confirmed=false;
            if ($parts==$confirmedParts)
            {
                DB::table('orders')
                         ->where('order_no',$request->order_no)
                         ->update(['confirmed'=>1]);
                $confirmed=true;

            }

       return response()->json(compact('confirmation','confirmed'));

   }
}
