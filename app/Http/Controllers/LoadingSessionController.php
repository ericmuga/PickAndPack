<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoadingSessionResource;
use App\Http\Resources\VesselResource;
use App\Models\{LoadingSession,User, Vehicle, Vessel};
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


   public function loadVessel()
   {
    dd('here');
   }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function loadSession(Request $request)
    {
        //show list of all vessels that have not been loaded
        // dd('here');
        $session=LoadingSession::find($request->id);
        //filter orders based on the session route

          $query= Vessel::get();//hereHas('order',fn($q)=>$q->where('sp_code',$session->sp_code))
                    //    ->doesntHave('loadingSession')
                        // >get();

        // $vessels=VesselResource::collection(Vessel::whereDoesntHave('loadingSession')->get());
        $vessels=VesselResource::collection($query);

        // dd($vessels);

        return inertia('Loading/Create',compact('vessels'));

    }

    public function load($id)
    {
       // this will link vessels to a loading session
     dd(LoadingSession::find($id));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());/
        $details=array_merge(['user_id'=>$request->user()->id],$request->all());
        // dd($details);
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
