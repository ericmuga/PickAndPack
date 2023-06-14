<?php

namespace App\Http\Controllers;

use App\Models\Prepack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrepackController extends Controller
{


    public function index(Request $request)
    {
        // this will list all the prepacks (as a config)


        //get item list
        $items=DB::table('items')->select('item_no','description')->get();

        $prepacks=Prepack::join('items','items.item_no','=','prepacks.item_no')
                         ->select('name','pack_size','isActive','prepacks.item_no','items.description')
                         ->get();
        return inertia('Prepacks/List',compact('prepacks','items'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name'=>'required|unique:prepacks',
            'item_no'=>'required',
            'pack_size'=>'required',
        ]);
        Prepack::create($request->all());
        return $this->index($request);
    }

    public function destroy($id)
    {
        Prepack::find($id)?->delete();
        return back();
    }

}
