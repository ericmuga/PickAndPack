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

                $orders= AssemblyOrderResource::collection(Order::query()
                                                        ->when($request->has('search'),fn($q)=>
                                                                $q->where('order_no','like','%'.$request->search)

                                                                )
                                                        ->whereHas('confirmations')
                                                            ->whereHas('assignmentLines', fn($q)=>$q->whereHas('assignment', fn($q)=>$q->where(

                                                                'assignee_id',$request->user()->id)))
                                                        ->shipcurrent()
                                                        ->orderByDesc('ending_date')
                                                        ->orderByDesc('ending_time')
                                                        // ->with('confirmations','assignmentLines')
                                                        ->paginate(5)
                                                        ->withQuerystring()

                                                    );


             return inertia('Orders/Assemble',['orders'=>$orders]);

      }


   // Get the items that belong to the order and part
    public function assembleOrder(Request $request)
    {

        $orderLines = Line::query()
                            ->where('order_no', $request->order_no)
                            ->where('part', $request->part_no)
                            ->withSum('prepacks', 'total_quantity')
                            ->with('order','assemblies')
                            ->orderBy('item_description')
                            ->paginate(300)
                            ->appends($request->all())
                            ->withQueryString();

        return inertia('Assembly/PartPackLines', [
                            'orderLines' =>LineResource::collection($orderLines),
                            'previousInput' => $request->all(),
                        ]);
    }


public function store(Request $request)
{

    //get assignment
   $user=Auth::user()->id;


    $part=Line::where('order_no',$request->data[0]['order_no'])
              ->where('line_no',$request->data[0]['line_no'])
              ->first()->part;

    $ass_id=AssignmentLine::where('order_no',$request->data[0]['order_no'])
                          ->where('part',$part)
                          ->first()->assignment_id;


     $session=AssemblySession::updateOrCreate([
                                                    'order_no'=>$request->data[0]['order_no'],
                                                    'part'=>$part,
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
