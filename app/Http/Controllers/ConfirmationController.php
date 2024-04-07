<?php

namespace App\Http\Controllers;

use App\Models\{Confirmation};

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\ConfirmationExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ConfirmationController extends Controller
{
    // use ExcelExportTrait;
public function download(Request $request)
    {
        return Excel::download(new ConfirmationExport($request), 'confirmations.xlsx');
    }


    public function refresh(Request $request)
    {

        try
        {
            // dd('here');
            DB::statement('EXEC dbo.refresh');
            $this->index($request);
        }
        catch(QueryException $e){
            $errorMessage = $e->getMessage();

            return back();
        }

    }

    public function index(Request $request)
    {    $orders=DB::table('pending_confirmation')
                ->select('order_no','shp_date','sp_code','sp_name','shp_name','A_Count','B_Count','C_Count','D_Count','A_Confirmation_Count','B_Confirmation_Count','C_Confirmation_Count','D_Confirmation_Count')
                ->where('shp_date','>=',now()->toDateString())
                // ->where('shp_date','>=',Carbon::yesterday()->toDateString())
                ->orderByDesc('ending_date')
                ->orderByDesc('ending_time')
                ->get();

    return inertia('Orders/List', [
            'orders' => $orders,
            'previousInput'=>$request->all(),

        ]);
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
