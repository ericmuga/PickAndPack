<?php

namespace App\Http\Controllers;

use App\Http\Resources\{LoadingResource,LoadingSummaryResource, OrderPackingResource};
use App\Http\Resources\LoadingSessionResource;
// use App\Http\Resources\LoadingOrderResource;
use App\Http\Resources\VesselResource;
use App\Models\{LoadingLine, LoadingSession, Order, PackingSessionLine, User, Vehicle, Vessel,Line};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Js;

class LoadingSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      //List loadingSessions
       $drivers=Cache::remember('drivers',60*60,fn()=>User::role('driver')->select('id','name')->get());
       $vehicles=Cache::remember('vehicles',60*60,fn()=>Vehicle::select('id','plate')->get());
       $loaders=Cache::remember('loaders',60*60,fn()=>User::role('loader')->select('id','name')->where('id','<>',$request->user()->id)->get());
       $spcodes=Cache::remember('Salespersons',60*60,fn()=>DB::table('sales_people')->select('name','code')->get());
       $sessions=DB::table('loading_session_list')
                    //  ->where('shp_date','>=',Carbon::today()->toDateString())
                     ->orderByDesc('loading_session_id')
                     ->get();
       return inertia('Loading/List',compact('sessions','drivers','vehicles','loaders','spcodes'));



    }

    public function loadingSheet(Request $request)
    {
    //get excel data for the session

    // dd($request);


     $session= LoadingSessionResource::make(LoadingSession::find($request->id));

     return response()->json($session);


    }


//    public function loadVessel(Request $request)
//    {
//       //check if there's a loading session for the current user

//      $lds=LoadingSession::where('user_id',$request->user()->id)->latest()->first();

//      if ($lds->count()>0)
//      {

//         //associate the load to the session
//         $srd= $request->order_no
//         Vessel::
//      }
// }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function loadSession(Request $request)
    {

        $session=LoadingSession::find($request->id);

        //display only vessels that belong to that route
        $orders=OrderPackingResource::collection(Order::where('shp_date', $session->shp_date)
                            ->where('sp_code', $session->sp_code)
                            ->whereHas('packing_sessions')
                            ->get());






        $packingLines=PackingSessionLine::whereHas('session',fn($q)=>$q->whereHas('order',fn($q)=>$q->where('sp_code',$session->sp_code)))
                                        ->with('session','packing_vessel');



        return inertia('Loading/Create',['packingLines'=>$packingLines,
                                         'session'=>LoadingSessionResource::make($session),
                                        'orders'=>$orders]);

    }

    public function load(Request $request)
    {
       // this will link vessels to a loading session

    //    dd($request->all());

      $session=LoadingSession::find($request->session_id);


    foreach($request->data as $line)
    {
        //update the loading lines

        DB::table('loading_lines')
          ->where('vessel_qr',$line['qr_code'])
          ->delete();




        LoadingLine::create([

                        'loading_session_id'=>$session->id,
                        'vessel_qr'=>$line['qr_code'],
                        'vessel_no'=>$line['vessel_no'],
                        'vessel'=>$line['vessel_type'],
                        ]);

    }

     if (!$request->autosave)
     {
        $session->update(['status'=>'complete']);
        return redirect(route('loadingSession.index'));

     }

    else
    {
        $session->update(['status'=>'pending']);
        return response('',200,[]);
    }

}




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $details=array_merge(['user_id'=>$request->user()->id],$request->all());
        // dd($details);

        //create if not found:same vehicle, same route, same day

        if (!LoadingSession::where('sp_code',$request->sp_code)
                      ->where('vehicle_id',$request->vehicle_id)
                      ->where('shp_date',$request->shp_date)
                      ->exists())
        LoadingSession::create($details);

        return redirect(route('loadingSession.index'));
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
