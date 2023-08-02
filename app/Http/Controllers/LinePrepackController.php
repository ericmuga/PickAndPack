<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinePrepackResource;
use App\Models\{LinePrepack,Item, Order,Line};
use Illuminate\Http\Request;
use App\Services\{SearchService,SearchQueryService};
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{PrepackExport};
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;

class LinePrepackController extends Controller
{

   public function index(Request $request)
   {

    //  dd('here');
       //list all prepacks with search functionality
        $prepackItems=Item::select('item_no','description')
                          ->whereHas('prepacks',fn($q)=>$q->where('isActive',true))
                          ->get();

        $previousInput=$request->all();





        $queryBuilder = LinePrepack::query(); // You can also use `Order::firstWhere('no', 2)` here
        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['batch_no','order_no','prepack_name'];
        $strictColumns = [];
        $relatedModels = [
            'order' => ['customer_name','shp_name','sp_name'],
            'user' => ['name'],
        ];

        $searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, $strictColumns, $relatedModels);

         $prepackLines = LinePrepackResource::collection($searchService->search()
                                                                        ->with(['line','order','user'])
                                                                        ->orderByDesc('order_no')
                                                                        ->paginate(15)
                                                                        ->withQueryString()
                                                        );

//  dd($prepackLines);

         $orders=OrderResource::collection($prepackLines->pluck('order')?->unique('order_no'));
        //  dd($orders);
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


    public function store(Request $request)
    {
      //insert line prepacks for the selected request parameters
    //   dd($request->all());
        $shp_date=$request->has('shp_date')?Carbon::parse($request->shp_date)->toDateString():Carbon::tomorrow()->toDateString();
    //    dd($request->shp_date);
        $prepack_ids=$request->prepack_ids;
        $sp_codes=$request->has('sp_code')?$request->sp_code:'';
        $order_nos=$request->has('order_no')?$request->order_no:'';
        $lines=Line::query()
                    ->whereHas('order',fn($q)=>$q->where('shp_date','=',$shp_date)
                                                 ->when($sp_codes!='',fn($q)=>$q->whereIn('sp_code',$sp_codes))
                                                 ->when($order_nos!='',fn($q)=>$q->whereIn('order_no',$order_nos))
                            )

                    ->get();

                    // dd($lines);
        foreach($lines as $line)
        {
          //insert prepack lines
          $prepacks=DB::table('prepacks')
            ->where('item_no',$line->item_no)
            ->where('isActive',true)
            ->whereIn('id',$prepack_ids)
            ->orderByDesc('pack_size')
            ->get();

                $batch_no=preg_replace('/\D/', '',Carbon::now()->toDateTimeString());
          foreach($prepacks as $prepack)
          {

                $count=intdiv($line->order_qty,$prepack->pack_size);
                if ($count>0)
                {
                    DB::table('line_prepacks')
                    ->insert([
                                'prepack_name'=>$prepack->prepack_name,
                                'line_no'=>$line->line_no,
                                'prepack_count'=>$count,
                                'total_quantity'=>$count*$prepack->pack_size,
                                'batch_no'=>$batch_no,
                                'carton_no'=>'1-'.$count,
                                'order_no'=>$line->order_no,
                                'user_id'=>$request->user()->id,

                            ]);
                }


          }
        }


      return redirect(route('linePrepacks.index',['search'=>$batch_no]));



    }
}
