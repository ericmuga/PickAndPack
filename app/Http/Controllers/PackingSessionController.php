<?php

namespace App\Http\Controllers;

use App\Http\Resources\{PackingOrderLineResource, PackingSessionResource,PackingOrderResource, PackingVesselResource, UserResource, VesselOrderResource};
use App\Models\{Assembly, AssemblySession, Line, PackingSession,Order, Packing, PackingSessionLine, PackingVessel, User,Vessel};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SearchQueryService;
// use Illuminate\Support\Facades\Auth;
use App\Exports\PackingSessionExport;
// use Doctrine\Common\Cache\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
class PackingSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function export()
    {
        return Excel::download(new PackingSessionExport, 'Sessions.xlsx');
    }

    public function index(Request $request)
    {
         $roles=Cache::remember('roles',30*60,fn()=>$request->user()->roles()->get()->pluck('name'));
         $date=$request->has('date')?$request->date:Carbon::today()->toDateString();
         $nextDay=$request->has('date')?Carbon::parse($request->date)->addDay(1):Carbon::tomorrow()->toDateString();
         $checkers=Cache::remember('checkers',30*60, fn()=>UserResource::collection(User::role('checker')->orderBy('name')->get()));
         $orders = DB::table('pending_packing')
                     ->where('shp_date','>=',Carbon::today()->toDateString())
                     ->get();


         $checker_id=$request->session()->get('checker_id')?:null;
        return inertia('PackingSession/List',compact('checkers','orders','roles','checker_id'));

    }


    public function store(Request $request)
    {
          if(!$request->session()->has('checker_id'))
            {
                if ($request->has('checker_id'))
                $request->session()->put('checker_id', $request->checker_id);
            }
            if($request->packing_session_id==0)
            {
                $session=PackingSession::create(array_merge($request->except('packing_session_id'),
                                                                ['user_id'=>$request->user()->id,
                                                                'packing_time'=>Carbon::createFromTime(0, 0, 0)
                                                                ]
                                                            )
                                                );
                return redirect(route('packingSession.show',$session->id));
            }
             else
                return redirect(route('packingSession.show',$request->packing_session_id));

    }

    public function closePacking(Request $request)
    {
        // dd($request->all());
        //save lines
        foreach ($request->lines as $line) {

            PackingSessionLine::create($line);

        }


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

            return response()->json($s,200,[]);




    }

    public function getLastVessel($id)
    {
        $s=PackingSession::find($id);
        $lastVessel=1;
        if($s->lines->count()>0)
        {
            $lastVessel=$s->lines->sortByDesc('to_vessel')->first()->to_vessel+1;
        }
        return response()->json($lastVessel,200,[]);
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

        $lines=$session->lines;


        $OrderLines=PackingOrderLineResource::collection(Line::where('order_no',$session->order_no)
                                                             ->where('part',$session->part)
                                                             ->get()
                                                        );

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
