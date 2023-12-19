<?php

namespace App\Http\Controllers;

use App\Http\Resources\{PackingSessionResource,PackingOrderResource, UserResource, VesselOrderResource};
use App\Models\{Line, PackingSession,Order,User};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackingSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //display the sessions
        // dd('here');


         $rows=$request->has('rows')?$request->row:10;

         $date=$request->has('date')?$request->date:Carbon::today()->toDateString();
         $nextDay=$request->has('date')?Carbon::parse($request->date)->addDay(1):Carbon::tomorrow()->toDateString();

         $sessions=PackingSessionResource::collection(
                               PackingSession::query()
                                //  ->where('created_at','>=',$date)
                                //  ->where('created_at','<=',$nextDay)
                                 ->with('order','user','checker')
                                 ->latest()
                                //  ->where('system_entry',0)
                                 ->paginate($rows)

                                );
            // dd($sessions);
           $checkers=UserResource::collection(User::role('checker')->orderBy('name')->get());

          $orders= VesselOrderResource::collection(Order::query()

                                                ->whereHas('assembly_sessions',fn($q)=>$q->where('system_entry',false))
                                                ->orderByDesc('ending_date')
                                                ->orderByDesc('ending_time')
                                                ->paginate(5)
                                                );



        return inertia('PackingSession/List',compact('rows','checkers','sessions','orders'));

    }


    public function store(Request $request)
    {
        if(Line::where('order_no',$request->order_no)
               ->where('part',$request->part)
               ->whereHas('order',fn($q)=>$q->whereHas('assembly_lines'))
               ->exists()
          )
        if(!PackingSession::where('order_no',$request->order_no)
               ->where('part',$request->part)
               ->exists()
          )
        PackingSession::create(array_merge($request->all(),['user_id'=>$request->user()->id,
                                                            'packing_time'=>Carbon::createFromTime(0, 0, 0)
                                                        ]
                                         )
                              );

        return redirect(route('packingSession.index'));
    }

    public function getOrderParts(Request $request )
    {
        return response()->json(DB::table('lines')->where('order_no',$request->order_no)->select('part')->distinct()->get(),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
