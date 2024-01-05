<?php

namespace App\Http\Controllers;

use App\Http\Resources\{PackingOrderLineResource, PackingSessionResource,PackingOrderResource, PackingVesselResource, UserResource, VesselOrderResource};
use App\Models\{Assembly, AssemblySession, Line, PackingSession,Order, PackingSessionLine, PackingVessel, User,Vessel};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SearchQueryService;
use Illuminate\Support\Facades\Auth;

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

         $todaysPackedTonnage= PackingSession::whereDate('created_at',Carbon::today())
                                            ->when((!$request->user()->hasRole('admin'))||(!$request->user()->hasRole('supervisor')),fn($q)=>$q->where('user_id',$request->user()->id))
                                            ->withSum('lines','weight')->get()->sum('lines_sum_weight')/1000;

         $packingStartTime=PackingSession::whereDate('created_at',Carbon::today())
                                         ->select('created_at')
                                         ->when((!$request->user()->hasRole('admin'))||(!$request->user()->hasRole('supervisor')),fn($q)=>$q->where('user_id',$request->user()->id))
                                         ->orderBy('created_at')->first();

        $packingEndTime=PackingSession::whereDate('created_at',Carbon::today())
                                         ->select('updated_at')
                                         ->when((!$request->user()->hasRole('admin'))||(!$request->user()->hasRole('supervisor')),fn($q)=>$q->where('user_id',$request->user()->id))
                                         ->orderByDesc('updated_at')->first();
//    dd($packingEndTime);
        if ($packingEndTime) {


                          $packingTime = Carbon::parse($packingEndTime->updated_at)
                                    ->diffInMinutes(Carbon::parse($packingStartTime->created_at));

                            } else {
                                $packingTime = null; // Handle the case when no result is found
                            }





        $roles=$request->user()->roles()->get()->pluck('name');

         $rows=$request->has('rows')?$request->row:10;
         $searchParameter=$request->search?:'';
        //  dd($searchParameter);

         $date=$request->has('date')?$request->date:Carbon::today()->toDateString();
         $nextDay=$request->has('date')?Carbon::parse($request->date)->addDay(1):Carbon::tomorrow()->toDateString();
         $packingSessionQuery=  PackingSession::query()
                                 ->when((!$request->user()->hasRole('admin'))||(!$request->user()->hasRole('supervisor')),fn($q)=>$q->where('user_id',$request->user()->id))
                                 ->latest();
         $searchService=new SearchQueryService($packingSessionQuery,$searchParameter,['order_no'],[],['order'=>['shp_name','customer_name']]);
         $sessions= PackingSessionResource::collection($searchService->with(['order','user','checker'])->search()->paginate($rows));
         $checkers=UserResource::collection(User::role('checker')->orderBy('name')->get());

          $orders= VesselOrderResource::collection(Order::query()

                                                ->whereHas('assembly_sessions',fn($q)=>$q->where('system_entry',false))

                                                ->orderByDesc('ending_date')
                                                ->orderByDesc('ending_time')
                                                ->paginate(5)
                                                );


        return inertia('PackingSession/List',compact('rows','checkers','sessions','orders','todaysPackedTonnage','packingTime','roles'));

    }


    public function store(Request $request)
    {

        if(Line::where('order_no',$request->order_no)
               ->where('part',$request->part)
               ->whereHas('order',fn($q)=>$q->whereHas('assembly_lines'))
               ->exists()
          )
          {
                if(!PackingSession::where('order_no',$request->order_no)
                    ->where('part',$request->part)
                    ->exists()
                    )
                    {
                        $session=PackingSession::create(array_merge($request->all(),['user_id'=>$request->user()->id,
                                                                            'packing_time'=>Carbon::createFromTime(0, 0, 0)
                                                                        ]
                                                        )
                                            );


                        return redirect(route('packingSession.show',$session->id));
                    }
                    else{
                        //order doesn't have that part
                    }
            }
            else{
                //order has no assembly lines error
            }

    }

    public function closePacking(Request $request)
    {
        // dd($request->all());
        $packingSession=PackingSession::find($request->id);
        $packingSession->system_entry=0;
        $packingSession->save();
        return redirect(route('packingSession.index'));
    }



    public function getOrderParts(Request $request )
    {
        //only assembled part
        $assembledParts=AssemblySession::where('order_no',$request->order_no)->select('part')->get();
        // dd($assembledParts);
        $packedParts=PackingSession::where('order_no',$request->order_no)->select('part')->get();

        return response()->json(DB::table('lines')
                                  ->where('order_no',$request->order_no)
                                  ->select('part')
                                  ->whereNotIn('part',$packedParts)
                                  ->whereIn('part',$assembledParts)
                                  ->get()->unique('part')
                                ,200
                                );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function getLines(Request $request)
    {
        $s=PackingSessionLine::where('packing_session_id',$request->id)->get();
        if($s->count>0)
            return response()->json($s->lines,200,[]);
        else
            return response()->json([],200,[]);




    }
    public function show($id)
    {
        //show a page with the session details and the vessels with lines
        $s=PackingSession::find($id);
        $lastVessel=1;
        if($s->lines->count()>0)
        {
            $lastVessel=$s->lines->sortByDesc('to_vessel')->first()->to_vessel+1;
        }

        $session= PackingSessionResource::make($s->load('lines','order','user','lines.packing_vessel'));
        // dd($session);
        // dd($session->order_no);
        $lines=$session->lines;


        $OrderLines=PackingOrderLineResource::collection(Line::where('order_no',$session->order_no)
                   ->where('part',$session->part)
                   ->get());

         $packingVessels=PackingVesselResource::collection(PackingVessel::latest()->get());
         $roles=auth()->user()->roles->pluck('name');

         $printedArray=Vessel::where('part',$session->part)->where('order_no',$session->order_no)->whereHas('logs')->get();
        return inertia('PackingSession/SessionCard',compact('session','OrderLines','lastVessel','packingVessels','lines','printedArray','roles'));


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
