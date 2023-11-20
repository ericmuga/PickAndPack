<?php

namespace App\Http\Controllers;

use App\Models\Vessel;
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
        //
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


        $vessel=Vessel::updateOrCreate([
                                        'part'=>$request->part,
                                        'order_no'=>$request->order_no,
                                        'vessel_type'=>$request->vessel_type,
                                        'vessel_no'=>$request->vessel_no,
                                       ],
                                       ['part'=>$request->part,
                                        'order_no'=>$request->order_no,
                                        'vessel_type'=>$request->vessel_type,
                                        'user_id'=>$request->user()->id,
                                        'vessel_no'=>$request->vessel_no
                                        ]


                                    );
       return response()->json($vessel);


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
