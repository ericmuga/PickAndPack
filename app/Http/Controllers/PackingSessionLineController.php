<?php

namespace App\Http\Controllers;

use App\Models\PackingSessionLine;
use App\Http\Requests\StorePackingSessionLineRequest;
use Illuminate\Http\Request;

class PackingSessionLineController extends Controller
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
     * @param  \App\Http\Requests\StorePackingSessionLineRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $line=PackingSessionLine::updateOrCreate($request->all());

        return redirect(route('packingSession.show',['packingSession'=>$request->packing_session_id]));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PackingSessionLine  $packingSessionLine
     * @return \Illuminate\Http\Response
     */
    public function show(PackingSessionLine $packingSessionLine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PackingSessionLine  $packingSessionLine
     * @return \Illuminate\Http\Response
     */
    public function edit(PackingSessionLine $packingSessionLine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePackingSessionLineRequest  $request
     * @param  \App\Models\PackingSessionLine  $packingSessionLine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $line=PackingSessionLine::find($id);
        $line->update($request->all());
        return redirect(route('packingSession.show',$line->packing_session_id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PackingSessionLine  $packingSessionLine
     * @return \Illuminate\Http\Response
     */
    public function destroy(PackingSessionLine $packingSessionLine)
    {
        $line=$packingSessionLine;
        $packingSessionLine->delete();
        return redirect(route('packingSession.show',$line->packing_session_id));

    }
}
