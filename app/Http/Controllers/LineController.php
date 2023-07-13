<?php

namespace App\Http\Controllers;

use App\Models\Line;
use App\Http\Requests\StoreLineRequest;
use App\Http\Requests\UpdateLineRequest;
use App\Http\Resources\LineResource;
use Illuminate\Http\Request;
use App\Services\SearchService;
class LineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function add(Request $request)
    {
        dd($request->all());
    }




     public function index(Request $request)
    {

         // Load all the lines with their prepack and assmblies

         $lines=LineResource::collection((new SearchService(new Line()))
         ->with(['prepacks','assemblies'])
         ->search($request));

         return inertia('Line\List',compact('lines'));

    }

    public function prepack(Request $request)

    {
        //prepack all the lines
        /**
         *
         * for each line get the prepackable quantity,
         */

        //  Lines::query()
        //       ->when($request->has('sales_person'));




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
     * @param  \App\Http\Requests\StoreLineRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLineRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Line  $line
     * @return \Illuminate\Http\Response
     */
    public function show(Line $line)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Line  $line
     * @return \Illuminate\Http\Response
     */
    public function edit(Line $line)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLineRequest  $request
     * @param  \App\Models\Line  $line
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLineRequest $request, Line $line)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Line  $line
     * @return \Illuminate\Http\Response
     */
    public function destroy(Line $line)
    {
        //
    }
}
