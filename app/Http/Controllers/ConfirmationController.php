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




     $orders=DB::table('pending_confirmation')
               ->select('order_no','shp_date','sp_code','sp_name','shp_name','A_Count','B_Count','C_Count','D_Count','A_Confirmation_Count','B_Confirmation_Count','C_Confirmation_Count','D_Confirmation_Count')
               ->where('shp_date','>=',now()->toDateString())
               ->orderByDesc('ending_date')
               ->orderByDesc('ending_time')
               ->get();




    return inertia('Orders/List', [
        'orders' => $orders,
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
                                                        ]);

         $confirmed=false;
             $rec=DB::table('pending_confirmation')
                    ->where('order_no',$request->order_no)
                    ->first();

              if ($rec!==null)
              {
                    if(($rec->A_Confirmation_Count+
                        $rec->B_Confirmation_Count+
                        $rec->C_Confirmation_Count+
                        $rec->D_Confirmation_Count
                    )>=
                    (
                        $rec->A_Count+
                        $rec->B_Count+
                        $rec->C_Count+
                        $rec->D_Count
                    ))
                    {
                        DB::table('orders')
                        ->where('order_no',$request->order_no)
                        ->update(['confirmed'=>1]);
                        $confirmed=true;

                    }
                }
                else $confirmed=true;


       return response()->json(compact('confirmed'));

   }
}
