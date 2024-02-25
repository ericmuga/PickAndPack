<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Http\Resources\{AssignmentOrderResource,AssignmentResource};
use Illuminate\Http\Request;
use App\Models\{Assignment,AssignmentLine,Order};
use Illuminate\Support\Facades\DB;
use App\Services\SearchQueryService;
use Illuminate\Support\Str;
use App\Services\ExcelService;
use Illuminate\Support\Facades\Cache;
use PhpParser\Node\Stmt\Switch_;
use App\Enums\CodeType;
use Illuminate\Support\Collection;
class AssignmentController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function export()
    {

    }

   public function getPending(){
     $orders = Cache::remember('pending_assignment', 15 * 60, function () {
                return DB::table('pending_assignment')
                    ->select('order_no', 'shp_date', 'sp_code', 'sp_name', 'shp_name', 'A_Weight', 'B_Weight', 'C_Weight', 'D_Weight')
                    ->where('shp_date', '>=', now()->toDateString())
                    ->get();
            });

    return response()->json(compact('orders'));


   }


    public function create(Request $request)
    {
      $orders =Cache::remember('pending_assignment', 15 * 60, function () {return
                                DB::table('pending_assignment')
                                    ->select('order_no',
                                            'shp_date',
                                            'sp_code',
                                            'sp_name',
                                            'shp_name',
                                            'A_Weight',
                                            'B_Weight',
                                            'C_Weight',
                                            'D_Weight',
                                            'A_Assignment_Count',
                                            'B_Assignment_Count',
                                            'C_Assignment_Count',
                                            'D_Assignment_Count')
                                    ->where('shp_date', '>=', now()->toDateString())
                                    ->get();
                            });


      $orders = $orders->filter($this->getFilterCondition($request))->values();
      $assignments=$this->generateAssignmentsArray($orders);
      $station=$request->station;
      $flag=$request->flag?:'';
      $assemblers=Cache::remember('assemblers', 15 * 60, function () {return DB::table('users')->select('name','id')->orderBy('name')->get();});
      return inertia('Assignment/Create',compact('orders' ,'assemblers','assignments','station','flag'));

    }


    private function getFilterCondition($request, $sp_code = null)
        {
            $condition = function ($item) {
                return $item->B_Weight > 0;
            };

            if (!$request->has('flag')) {
                if ($request->station == 'a') {
                    $condition = function ($item) {
                        return $item->A_Weight > 0 || $item->C_Weight > 0 || $item->D_Weight > 0;
                    };
                }
            } else {
                switch ($request->flag) {
                    case 'horeca':
                        $sp_code = CodeType::MAP['HORECA'];
                        break;
                    case 'retail':
                        $sp_code = CodeType::MAP['RETAIL'];
                        break;
                    case 'msa':
                        $sp_code = CodeType::MAP['MSA'];
                        break;
                    case 'upc':
                        $sp_code = CodeType::MAP['UPC'];
                        break;
                    case 'ff':
                        $sp_code = CodeType::MAP['F_FOOD'];
                        break;
                    default:
                        // Handle the default case if needed
                        break;
                }

                if (isset($sp_code)) {
                    if ($request->station == 'a') {
                        $condition = function ($item) {
                            return $item->A_Weight > 0 || $item->C_Weight > 0 || $item->D_Weight > 0;
                        };
                    }
                }
            }

            return $condition;
        }


    private function generateAssignmentsArray($orders)
    {
         $assignments=collect([]);

        foreach ($orders as $order)
        {
            if ($order->A_Assignment_Count>0)
            {
                $assignments->push(['order_no'=>$order->order_no,'A','weight'=>$order->A_Weight]);
            }
            if ($order->B_Assignment_Count>0)
            {
                $assignments->push(['order_no'=>$order->order_no,'B','weight'=>$order->B_Weight]);
            }
            if ($order->C_Assignment_Count>0)
            {
                $assignments->push(['order_no'=>$order->order_no,'C','weight'=>$order->C_Weight]);
            }
            if ($order->D_Assignment_Count>0)
            {
                $assignments->push(['order_no'=>$order->order_no,'D','weight'=>$order->D_Weight]);
            }
        }
        return $assignments;
    }

    public function index(Request $request)
    {
        //list all the assignments that are on going
       $queryBuilder=Assignment::when($request->has('assemblers'),fn($q)=>$q->whereIn('assignee_id',$request->assemblers))
                                                               ->when($request->has('date')&&($request->date<>''),
                                                                           fn($q)=>$q->where('created_at','>=',Carbon::parse($request->date)->toDateString())
                                                                                     ->where('created_at','<=',Carbon::parse($request->date)->addDay(1)->toDateString()))
                                                               ->when(!$request->has('date')||($request->date==''),
                                                                           fn($q)=>$q->where('created_at','>=',Carbon::today()->toDateString())
                                                                                     ->where('created_at','<=',Carbon::tomorrow(1)->toDateString()));


        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['id'];
        $strictColumns = [];
        $relatedModels = [
                            'lines' => ['order_no'],
                            'assignee' => ['name'],
                        ];



        $searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, $strictColumns,$relatedModels);
        // dd($searchService);
        $assignments = $searchService
                        ->with(['lines','assignee','assignor','lines.order']) // Example of eager loading related models
                        ->search()
                        ->withCount('lines')
                        ->latest()
                        ->paginate(20)
                        ->appends($request->all());

        $assignments= AssignmentResource::collection($assignments);

        //list of assemblers
        $dateParam=$request->has('date')?$request->date:Carbon::today()->toDateString();
        $assemblersParam=$request->has('assemblers')?$request->assemblers:[];
       $assemblers=DB::table('users')->select('name','id')->orderBy('name')->get();

        return inertia('Assignment/List',compact('assignments','assemblers','assemblersParam','dateParam'));
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $ass=Assignment::create([
                'assignee_id'=>$request->assignee,
                'assignor_id'=>$request->user()->id,
                'batch_no'=>Str::uuid()

       ]);

        foreach($request->parts as $p)
        {

            AssignmentLine::create([
                  'assignment_id'=>$ass->id,
                  'batch_no'=>$ass->batch_no,
                  'part'=>$p['part'],
                  'order_no'=>$p['order_no'],
            ]);
        }
       //check if order has been fully assigned for that station,
       $assigned=false;
       if ($request->station='b') $assigned=true;
       else{
          $order_parts=DB::table('order_parts')
                   ->where('order_no',$request->part[0]->order_no)
                   ->where('part','<>','B')
                   ->count();
          $assigned= AssignmentLine::where('order_no',$request->part[0]->order_no)
                                   ->where('part','<>','B')
                                   ->count();
         if ($order_parts<=$assigned)
             $assigned=true;
       }
              return response()->json([compact('assigned')],200);

    }


    public function show($id)
    {

       //show the contents of the assignment
       $assignment=AssignmentResource::make(Assignment::find($id)->load('lines','lines.order'));

       return response()->json($assignment);



    }

    public function edit($id)
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
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        Assignment::delete($id);
        return redirect('assignment.index');
    }
}
