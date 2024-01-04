<?php

namespace App\Http\Controllers;

use App\Http\Resources\VesselOrderResource;
use App\Models\AssemblyLine;
use App\Models\Order;
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
