<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{AssemblyOrderResource,LineResource};
use App\Models\{Assembly, Order,Line,AssemblySession,AssemblyLine, Assignment, AssignmentLine};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\MyServices;
use Illuminate\Support\Facades\Auth;

class AssemblyController extends Controller
{


    //this will show all orders pending assembly

    public function index(Request $request)
    {

        $orders= DB::table('pending_assembly')
                                    ->select('order_no',
                                            'shp_date',
                                            'sp_code',
                                            'sp_name',
                                            'shp_name',
                                            'assignee_id',
                                            'A_Assignment_Count',
                                            'B_Assignment_Count',
                                            'C_Assignment_Count',
                                            'D_Assignment_Count',

                                            'A_Assembly_Count',
                                            'B_Assembly_Count',
                                            'C_Assembly_Count',
                                            'D_Assembly_Count'
                                            )
                                    ->where('shp_date', '>=', now()->toDateString())
                                    ->where('assignee_id', '=', $request->user()->id)
                                    ->get();

    //    dd($orders);
             return inertia('Orders/Assemble',['orders'=>$orders]);

      }


   // Get the items that belong to the order and part
    public function assembleOrder(Request $request)
    {

       //cre
        $orderLines = Line::query()
                            ->select('item_no',
                                    'item_description',
                                    'line_no',
                                    'order_qty',
                                    'orders.order_no',
                                    'barcode',
                                    'qty_base',
                                    'shp_name',
                                    'part',
                                    DB::raw("CONCAT(orders.sp_code, orders.sp_name) AS sp_search_name"))
                            ->join('orders', 'orders.order_no', '=', 'lines.order_no')
                            ->with('assemblies')
                            ->where('lines.order_no', $request->order_no)
                            ->where('part', $request->part_no)
                            ->orderBy('item_description')
                            ->get();
                            // dd($orderLines);

        return inertia('Assembly/PartPackLines', compact('orderLines'));
    }


public function store(Request $request)
{

    //get assignment
   $user=Auth::user()->id;




    $ass_id=AssignmentLine::where('order_no',$request->data[0]['order_no'])
                          ->where('part',$request->part)
                          ->first()->assignment_id;


     $session=AssemblySession::updateOrCreate([
                                                    'order_no'=>$request->data[0]['order_no'],
                                                    'part'=>$request->part,
                                                    'system_entry'=>$request->autosave,
                                                    ],
                                                    [
                                                    'assembly_time'=>$request->assembly_time,
                                                    'user_id'=>$user,
                                                    'assignment_id'=>$ass_id,
                                                    ]

                                                );

    //create assembly lines
    foreach($request->data as $line)
    {
         // dd($line);

        DB::table('assembly_lines')
          ->where('line_no',$line['line_no'])
          ->where('order_no',$line['order_no'])
          ->delete();

        AssemblyLine::create([
                                'order_no'=>$line['order_no'],
                                'line_no'=>$line['line_no'],
                                'from_batch'=>MyServices::preventNullsFromArray('from_batch',$line),
                                'to_batch'=>MyServices::preventNullsFromArray('to_batch',$line)?:MyServices::preventNullsFromArray('from_batch',$line),
                                'assembly_session_id'=>$session->id,
                                'user_id'=>$user,
                                'ass_qty'=>MyServices::zeroIfNullOrBlank('assembled_qty',$line,0),
                                'ass_pcs'=>intVal(MyServices::zeroIfNullOrBlank('assembled_pcs',$line,0)),
                              ]);
         }

     if (!$request->autosave)
      return redirect(route('assembly.index'));
    else
      return response('',200,[]);

  }







}
