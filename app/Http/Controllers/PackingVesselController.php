<?php

namespace App\Http\Controllers;

use App\Models\PackingVessel;
use Illuminate\Http\Request;
use App\Services\SearchQueryService;
use App\Http\Resources\PackingVesselResource;

class PackingVesselController extends Controller
{

    public function index(Request $request)
    {
        //  $columns = ['code','description','tare_weight','blocked','id'];
        $queryBuilder = PackingVessel::query();
        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['code','description'];
        $searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, [], []);
        // dd($searchService);
        $packingVessels = PackingVesselResource::collection($searchService->search()->latest()->paginate(10));
        // dd($packingVessels);
        return inertia('PackingVessel/List', compact('packingVessels'));

  }

    public function store(Request $request)
    {
    //    dd($request->all());
        PackingVessel::updateOrCreate($request->all());
        return redirect(route('packingVessel.index'));
    }


    public function update(Request $request, $id)
    {
        PackingVessel::find($id)->update($request->all());
        return redirect(route('packingVessel.index'));
    }

    public function destroy($id)
    {
        $vessel=PackingVessel::firstWhere('code',$id);
        $vessel->delete();
        return redirect(route('packingVessel.index'));
    }
}
