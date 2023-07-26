<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinePrepackResource;
use App\Models\{LinePrepack,Item, Order};
use Illuminate\Http\Request;
use App\Services\{SearchService,SearchQueryService};
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{PrepackExport};
use App\Http\Resources\OrderResource;

class LinePrepackController extends Controller
{

   public function index(Request $request)
   {

       //list all prepacks with search functionality
        $prepackItems=Item::select('item_no','description')
                          ->whereHas('prepacks',fn($q)=>$q->where('isActive',true))
                          ->get();

        $previousInput=$request->all();

    // $prepackLines= LinePrepack::whereHas('order',fn($q)=>$q->current()
        //                                                        ->where('confirmed',true)
        //                                     );


        $prepackLines=LinePrepackResource::collection((new SearchService(new LinePrepack()))
                                                     ->with(['line','order','user'])
                                                     ->search($request)
                                                    );

         $orders=OrderResource::collection($prepackLines->pluck('order')->unique('order_no'));
         $sp_codes=OrderResource::collection($prepackLines->pluck('order')->unique('sp_code'));

       $prepackBatches=LinePrepackResource::collection(LinePrepack::select('created_at','batch_no')->orderByDesc('id')->get()->unique('batch_no'));


      return inertia('LinePrepacks/List', compact('prepackItems','previousInput','prepackLines','prepackBatches','orders','sp_codes'));

   }

   public function show($batch_no)
   {
    //get all prepacks of a given batch number in one page, with the creator and other meta-data


   }

   public function export(Request $request)
    {
            ob_end_clean(); // this
            ob_start(); // and this

       return Excel::download(new PrepackExport($request->all()), 'prepacks.xlsx',\Maatwebsite\Excel\Excel::XLSX);

    }

}
