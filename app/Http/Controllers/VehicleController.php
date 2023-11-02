<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Http\Resources\VehicleResource;
use App\Services\SearchQueryService;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        dd('under maintenance');
        // return Excel::download(new ConfirmationExport([$request->from,$request->to]), 'confirmations.xlsx');
        // return Excel::download(new ConfirmationExport(['2023-06-09','2023-06-09']), 'confirmations.xlsx');
    }




    public function index(Request $request)
    {
        //list all the vehicles
        $query= Vehicle::latest();
        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['plate_no','fleet_no'];
        $strictColumns = [];
        $relatedModels = [
                            // 'relatedModel1' => ['related_column1', 'related_column2'],
                            // 'relatedModel2' => ['related_column3'],
                         ];



        $searchService = new SearchQueryService($query, $searchParameter, $searchColumns, $strictColumns, $relatedModels);
        // dd($searchService);
        $vehicles = $searchService
                // ->with(['permissions','roles']) // Example of eager loading related models
                ->search();
        $rows=$request->has('rows')?$request->rows:10;


    //   dd($vehicles);

        $vehicles= VehicleResource::collection($vehicles->paginate($rows));
        // $roles= RoleResource::collection(Role::all());
        // $permissions=PermissionResource::collection(Permission::all());

        return inertia('Vehicle/List',compact('vehicles'));

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
        Vehicle::create($request->all());
        return redirect(route('vehicles.index'));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
               Vehicle::firstWhere('plate',$id)?->update($request->all());
        return redirect (route('vehicles.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vehicle=Vehicle::find($id);
        $vehicle->delete();
        return redirect(route('vehicles.index'));
    }
}
