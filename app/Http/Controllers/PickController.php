<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\{Pick, Order, Line, LinePrepack, PickOrder,AssemblyLine,AssignmentLine,AssemblySession};
use App\Http\Resources\{LineResource};
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\ToArray;
// use App\Services\MyServices;

class PickController extends Controller
{

    public function index(Request $request)

    {

        //display all picks for the day

         $picks =Pick::select('id','status')->where('user_id',$request->user()->id)
                    ->latest()
                    ->orderByDesc('status')
                    ->withCount(['lines'])
                    ->get();





        return inertia('Picks/List', compact('picks'));
    }

    public function show(Pick $pick)
    {

        $lines=DB::table('lines')
                ->select('item_description','customer_spec','barcode','item_no','pick_id',DB::raw('SUM(order_qty) as group_qty'))
                ->where('pick_id',$pick->id)
                ->groupBy('item_description','customer_spec','item_no','barcode','pick_id')
                ->get();

        return inertia('Picks/PartPackLines',compact('pick','lines'));
    }

    private function getPrepacks($orderNos,$line)
    {
      LinePrepack::whereIn('order_no',$orderNos)->where('item_no')
                 ->join('line',fn($q)=>$q->on('line_no',$line->line_no)->on('order_no','order_no'))
                 ->sum('total_quantity');

    }

    public function store(Request $request)
    {

        // dd($request->all());

         $user=$request->user()->id;
         $firstLine=Line::firstWhere('pick_id',$request->pick_id);
         $ass_id=AssignmentLine::where('order_no',$firstLine->order_no)
                                ->where('part',$firstLine->part)
                                ->first()->assignment_id;
         $orders=Line::select('order_no','part')->where('pick_id',$request->pick_id)->groupBy('order_no','part')->get();
         foreach($orders as $order)
            {
                 $session=AssemblySession::updateOrCreate([
                                                            'order_no'=>$order['order_no'],
                                                            'part'=>$order['part'],
                                                            'system_entry'=>false,
                                                            ],
                                                            [
                                                            'user_id'=>$user,
                                                            'assignment_id'=>$ass_id,
                                                            'assembly_time'=>'00:00:00'
                                                            ]

                                                        );

            }
           $lines=Line::where('pick_id',$request->pick_id)
                       ->select('line_no','order_no','order_qty','qty_base')
                       ->get();

           foreach($lines as $line)
           {
             AssemblyLine::where('line_no',$line['line_no'])->where('order_no',$line['order_no'])->delete();
           }

            foreach($request->data as  $dataLine)
            {

                $lines=Line::where('pick_id',$request->pick_id)
                           ->where('item_no',$dataLine['item_no'])
                           ->select('line_no','order_no','order_qty','qty_base')
                           ->get();


               $assembledQty=floatval($dataLine['assembled_qty']);
               $assembledPcs=floatval($dataLine['assembled_pcs']);

               foreach($lines as $orderLine)
               {
                 if ($assembledQty>0)
                 {
                    $qty_to_assemble=0;
                    if(floatval($orderLine['qty_base'])<=$assembledQty)
                    {
                        $qty_to_assemble=floatval($orderLine['qty_base']);
                    }
                    else $qty_to_assemble=$assembledQty;

                    $pcs_to_assemble=0;
                    if(floatval($orderLine['order_qty'])<=$assembledPcs)
                    {
                        $pcs_to_assemble=floatval($orderLine['order_qty']);
                    }
                    else $pcs_to_assemble=$assembledPcs;

                    if ($qty_to_assemble>0)
                     {
                        AssemblyLine::create([
                                        'order_no'=>$orderLine['order_no'],
                                        'line_no'=>$orderLine['line_no'],
                                        'from_batch'=>$dataLine['from_batch'],
                                        'to_batch'=>$dataLine['to_batch'],
                                        'assembly_session_id'=>$session->id,
                                        'user_id'=>$user,
                                        'ass_qty'=>$qty_to_assemble,
                                        'ass_pcs'=>$pcs_to_assemble,
                                    ]);
                        $assembledQty-=$qty_to_assemble;
                        $assembledPcs-=$pcs_to_assemble;

                     }
                     else break;

                 }
                }


            }




        return redirect(route('assembly.index'));
        }


}
