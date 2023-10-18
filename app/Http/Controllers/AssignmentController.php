<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Http\Resources\{AssignmentOrderResource,AssignmentResource};
use Illuminate\Http\Request;
use App\Models\{Assignment,AssignmentLine,Order};
use Illuminate\Support\Facades\DB;
use App\Services\SearchQueryService;
use Illuminate\Support\Str;
class AssignmentController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create(Request $request)
    {

        $records=$request->records?:5;

        $orders = AssignmentOrderResource::collection(Order::shipcurrent()
                                                            ->when($request->has('spcodes')&&($request->spcodes<>''),fn($q)=>$q->whereIn('sp_code',$request->spcodes))
                                                            ->when($request->has('shp_date')&&($request->shp_date<>''),fn($q)=>$q->where('shp_date',Carbon::parse($request->shp_date)->toDateString()))
                                                            ->select('shp_name', 'order_no', 'shp_date', 'sp_code')
                                                            ->with(['lines','assignmentLines'])
                                                            ->withCount(['assignmentLines','confirmations'])
                                                            ->paginate($records)
                                                            ->appends($request->all())
                                                            ->withQueryString()
                                                    );


        $spcodes=DB::table('sales_people')->select('name','code')->get();

        $assemblers=DB::table('users')->select('name','id')->get();



        return inertia('Assignment/Create',compact('orders' ,'spcodes','assemblers'));

    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */


    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */




    public function index()
    {
        //list all the assignments that are on going

        $assignments= AssignmentResource::collection(Assignment::with('assignee','assignor')
                                                               ->withCount('lines')
                                                               ->latest()
                                                               ->paginate(15));

        //list of assemblers


        return inertia('Assignment/List',compact('assignments'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

      // create the assignment
        //$batchNo
       $ass=Assignment::create([
                'assignee_id'=>$request->assignee,
                'assignor_id'=>$request->user()->id,
                'batch_no'=>Str::uuid()

       ]);



      //create the lines




        foreach($request->selectedParts as $p)
        {

            AssignmentLine::updateOrCreate([
                 'assignment_id'=>$ass->id,
                 'batch_no'=>$ass->batch_no,
                'part'=>$p['part'],
                'order_no'=>$p['order_no'],
            ]);
        }
        return redirect(route('assignment.index'));

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
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
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
