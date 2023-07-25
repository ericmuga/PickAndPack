<?php

namespace App\Http\Controllers;

use App\Models\Prepack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ColumnListing;
use App\Http\Resources\PrepackResource;

class PrepackController extends Controller
{




    public function index(Request $request)
    {
        //this will list all the prepacks (as a config)
    //    $cols=new ColumnListing('prepacks');
    //    dd($cols->getColumns());


        //get item list
        $items=DB::table('items')->select('item_no','description')->get();


        $prepacks=PrepackResource::collection(Prepack::query()
                                ->when($request->has('search'),fn($q)=>$q->where('item_no','like','%'.$request->search.'%')
                                                                        ->orWhereHas('item',fn($query)=>$query->where('description','like','%'.$request->search.'%'))
                                                                        ->when(is_numeric($request->search),fn($q)=>$q->where('pack_size','=',$request->search))
                                    )
                                ->with('item')
                                ->withCount('linePrepacks')
                                ->paginate(15)
                                ->withQueryString());



        return inertia('Prepacks/List',compact('prepacks','items'));
    }

    public function store(Request $request)
    {
        // dd($request->all());





        $request->validate([
            'prepack_name'=>'required|unique:prepacks',
            'item_no'=>'required',
            'pack_size'=>'required',
        ]);
        Prepack::create($request->all());
        return $this->index($request);
    }

    public function update(Request $request)
    {
       $prepack=Prepack::firstWhere('prepack_name',$request->prepack_name);
       if ($prepack->has('linePrepacks')) $prepack->update($request->only('isActive'));
       else $prepack->update($request->all());
       return redirect(route('prepacks.index'));
    }

    public function destroy($prepack)
    {
        $prepack=Prepack::where('prepack_name',$prepack)
                        ->doesntHave('linePrepacks')
                        ->first();

    if($prepack->count()>0)
    {
        if ($prepack->delete())
          return redirect(route('prepacks.index'))->withErrors(['success'=>'Item Deleted!']);
        else
         return back()->withErrors(['message'=>'An error occurred during the deletion']);
    }
}

}
