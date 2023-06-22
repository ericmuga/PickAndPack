<?php

namespace App\Exports;

use App\Models\LinePrepack;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;

class PrepackExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $shp_date='';
    public $sector='';
    public $sp_code='';
    public $item_no='';
    public $order_no='';
    public $batch_no='';

    public function __construct(Request $request)
    {
        dd($request->batch_no);
        if ($request->has('item_no')){$this->item_no=$request->item_no;}
        if ($request->has('batch_no')){$this->item_no=$request->batch_no;}
        if ($request->has('order_no')){$this->item_no=$request->order_no;}
        if ($request->has('sp_code')){$this->item_no=$request->sp_code;}
        if ($request->has('shp_date')){$this->item_no=$request->shp_date;}
        if ($request->has('sector')){$this->item_no=$request->sector;}

    }

    public function collection()
    {
        return LinePrepack::query()
                            ->when($this->order_no<>'',fn($q)=>$q->where('order_no',$this->order_no))
                            ->when($this->batch_no<>'',fn($q)=>$q->where('batch_no',$this->batch_no))
                            //    ->when($this->batch_no<>'',fn($q)=>$q->where('batch_no',$this->batch_no))
                        ->get();

        // return LinePrepack::all();
    }
}
