<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackingSessionResource;
use App\Models\PackingSession;
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

         $packersList=DB::table('users')->select('name','id')->orderBy('name')->get();
         $rows=$request->has('rows')?$request->row:10;
         $packers=$request->has('packers')?$request->packers:[];
         $date=$request->has('date')?$request->date:Carbon::today()->toDateString();
         $nextDay=$request->has('date')?Carbon::parse($request->date)->addDay(1):Carbon::tomorrow()->toDateString();

         $sessions=PackingSessionResource::collection(PackingSession::where('created_at','>=',$date)
                                 ->where('created_at','<=',$nextDay)
                                 ->when($request->has('packers')&&($packers!=[]),fn($q)=>$q->whereIn('user_id',$packers))
                                 ->with('order','user')
                                 ->where('system_entry',0)
                                 ->paginate($rows)
                                //  ->appends($request->all())
                                );

        return inertia('PackingSession/List',compact('rows','packers','sessions','date','packersList'));

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
