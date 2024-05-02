<?php

namespace App\Http\Controllers;

use App\Http\Resources\VesselOrderResource;
use App\Models\AssemblyLine;
use App\Models\Order;
use App\Models\PackingSessionLine;
use App\Models\PackingVessel;
use App\Models\User;
use App\Models\Vessel;
use App\Models\VesselLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VesselController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //show a list of all vessels
        //get orders that have been assembled
        $orders=VesselOrderResource::collection(Order::shipCurrent()
                    ->whereHas('assembly_lines')
                    ->with('lines')
                    ->get() );
       $packers=User::select('name','id')->role('packer')->get();
       $checkers=User::select('name','id')->role('checker')->get();

       return inertia('Vessel/VesselForm',compact('orders','packers','checkers'));

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
    //   dd($request->all());


        //check if vessel already exists
       $vessel=Vessel::findRecord([ 'part'=>$request->part,
                                    'order_no'=>$request->order_no,
                                    'vessel_type'=>$request->vessel_type,
                                    'vessel_no'=>$request->vessel_no,
                                 ]);

        if (!$vessel)
          {$vessel=Vessel::Create(
                                       ['part'=>$request->part,
                                        'order_no'=>$request->order_no,
                                        'vessel_type'=>$request->vessel_type,
                                        'user_id'=>$request->user()->id,
                                        'vessel_no'=>$request->vessel_no,
                                        'range_start'=>$request->range_start,
                                        'range_end'=>$request->range_end,
                                        ]
                                    );
          }
        else
        {
            if (!($request->user()->hasRole('admin')|| $request->user()->hasRole('supervisor')))
              return response()->json(['error'=>'Label reprints must me initiated by an admin or supervisor'],'401');
        }
        //log the printing
        VesselLog::create(['user_id'=>$request->user()->id,'vessel_id'=>$vessel->id]);


       //get vessel ranges for that part

       $printed=Vessel::where('part',$request->part)->where('order_no',$request->order_no)->whereHas('logs')->get();

       return response()->json(['data'=>$printed]);


    }

    public function remove(Request $request)
    {
        //  dd($request->only('vessel_type','range_start','range_end'));
        Vessel::where($request->only('vessel_type','range_start','range_end','order_no'))->delete();
        // VesselLog::where()


        //   $v=PackingVessel::firstWhere('code',$request->vessel_type);
          PackingSessionLine::where('packing_session_id',$request->packing_session_id)
                            ->where('order_no',$request->order_no)
                            ->where('from_vessel',$request->range_start)
                            ->where('to_vessel',$request->range_end)
                            ->where('packing_vessel_id',$request->vessel_no)
                            ->delete();

       $printed=Vessel::where('part',$request->part)->where('order_no',$request->order_no)->get();
       return response()->json(['data'=>$printed]);
    }

    public function upload(Request $request)
    {
        $uploadedFile = $request->file('pdfFile');
        $pdfDataUri = $request->input('pdfDataUri');
        $timestamp = preg_replace('/[-:]/', '',Carbon::now()->toDateTimeString());

        // Save the uploaded PDF file to the storage
        $path = $uploadedFile->storeAs('pdfs',$timestamp.$request->order.'.pdf');

        // You can also save additional information related to the PDF in your database if needed

        return response()->json(['message' => 'File uploaded successfully', 'path' => $path]);
    }

    public function voidVessel(Request $request)
{
    $query = Vessel::query();

    // Extract parameters from the request
    $range_start = $request->input('range_start');
    $range_end = $request->input('range_end');
    $vessel_type = $request->input('vessel_type');
    $vessel_id = $request->input('vessel_id');
    $order_no = $request->input('order_no');
    $part = $request->input('part');

    // Add conditions to the query
    if ($range_start !== null) {
        $query->where('range_start', $range_start);
    }

    if ($range_end !== null) {
        $query->where('range_end', $range_end);
    }

    if ($vessel_type !== null) {
        $query->where('vessel_type', $vessel_type);
    }

    if ($order_no !== null) {
        $query->where('order_no', $order_no);
    }
    if ($part !== null) {
        $query->where('part', $part);
    }

    // Execute delete operation
    $query->delete();


    //delete from packing session lines
    PackingSessionLine::where('packing_session_id',$request->packing_session_id)
                      ->where('order_no',$order_no)
                      ->where('from_vessel',$range_start)
                      ->where('to_vessel',$range_end)
                      ->where('packing_vessel_id',$vessel_id)
                      ->delete();

    return response()->json(['message' => 'Vessels deleted successfully']);
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
