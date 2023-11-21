<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoadingResource;
use App\Http\Resources\LoadingSessionResource;
use App\Http\Resources\VesselResource;
use App\Models\{LoadingLine, LoadingSession, Order, User, Vehicle, Vessel};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
       $drivers=User::role('driver')->select('id','name')->get();
    //    $drivers=User::select('id','name')->get();
       $vehicles=Vehicle::select('id','plate')->get();
       $loaders=User::role('loader')->select('id','name')->where('id','<>',$request->user()->id)->get();


       $rows=$request->rows?:10;
        $spcodes=DB::table('sales_people')->select('name','code')->get();
       $sessions= LoadingSessionResource::collection(LoadingSession::with('lines','SalesPerson')->paginate($rows));

       return inertia('Loading/List',compact('sessions','drivers','vehicles','loaders','spcodes'));



    }


   public function loadVessel(Request $request)
   {
      //check if there's a loading session for the current user

      $lds=LoadingSession::where('user_id',$request->user()->id)
                    ->latest();
    dd($lds);







   }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function loadSession(Request $request)
    {

        $session=LoadingSession::find($request->id);

        //display only vessels that belong to that route


        $orders=Order::where('shp_date',$session->shp_date)
                     ->where('sp_code',$session->sp_code)
                     ->select('order_no')->get();
// dd($orders->pluck('order_no'));

        $query= Vessel::whereIn('order_no',$orders->pluck('order_no'))
                    //   ->with(['order'=>fn($q)=>$q->select('shp_name')])
                      ->get();
// dd($query);

        $vessels=VesselResource::collection($query);


        return inertia('Loading/Create',['vessels'=>$vessels,'session'=>LoadingSessionResource::make($session)]);

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
