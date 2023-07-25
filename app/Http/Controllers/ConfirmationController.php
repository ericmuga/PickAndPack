<?php

namespace App\Http\Controllers;

use App\Models\Confirmation;
use App\Http\Resources\ConfirmationResource;
use App\Traits\ExcelExportTrait;
use Illuminate\Http\Client\Request;


class ConfirmationController extends Controller
{
    use ExcelExportTrait;

    public function export()
    {
        $data = Confirmation::first()->toArray(); // Replace with your actual Model

        $filename = 'data_export_' . date('Ymd_His') . '.xlsx';

        return $this->exportExcel($data, ConfirmationResource::class, $filename);


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
     * @param  \App\Http\Requests\StoreConfirmationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConfirmationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Confirmation  $confirmation
     * @return \Illuminate\Http\Response
     */
    public function show(Confirmation $confirmation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Confirmation  $confirmation
     * @return \Illuminate\Http\Response
     */
    public function edit(Confirmation $confirmation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateConfirmationRequest  $request
     * @param  \App\Models\Confirmation  $confirmation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConfirmationRequest $request, Confirmation $confirmation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Confirmation  $confirmation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Confirmation $confirmation)
    {
        //
    }
}
