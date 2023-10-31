<?php

namespace App\Http\Controllers;

use App\Exports\ItemsExport;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\Prepack;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function download()
    {
        // dd('here');
        return Excel::download(new ItemsExport(),'itemList.xlsx');

    }


     public function index(Request $request)
    {
         $items=ItemResource::collection(Item::query()
                                             ->when($request->has('search'),fn($q)=>$q->where('item_no','like','%'.$request->search.'%')
                                                                                      ->orWhere('barcode','like','%'.$request->search.'%')
                                                                                      ->orWhere('description','like','%'.$request->search.'%')
                                                                                      ->orWhere('posting_group','like','%'.$request->search.'%')
                                               )
                                              ->with('prepacks')->paginate(15));

         return inertia('Item/List',['items'=>$items]);



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
        $request->validate(['item_no'=>'required|unique:items',
                            'description'=>'required|unique:items',
                            'barcode'=>'required|unique:items']);

        Item::create($request->all());
        $this->index($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\r  $r
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\r  $r
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)

    {

        Item::firstWhere('item_no',$request->item_no)?->update($request->all());
        return redirect (route('items.index'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\r  $r
     * @return \Illuminate\Http\Response
     */
    public function destroy($item)
    {
        Prepack::where('item_no',$item)->delete();
        Item::firstWhere('item_no',$item)?->delete();
        return redirect(route('items.index'));
    }
}
