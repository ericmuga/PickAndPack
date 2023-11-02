<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoadingResource;
use App\Models\{LoadingSession,User, Vehicle};
use Illuminate\Http\Request;

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
       $sessions= LoadingResource::collection(LoadingSession::with('lines')->paginate($rows));

       return inertia('Loading/List',compact('sessions','drivers','vehicles','loaders'));



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function load(Request $request)
    {
      dd($request->all());
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
        LoadingSession::create($request->all());
        return redirect('loadingSession.index');
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
