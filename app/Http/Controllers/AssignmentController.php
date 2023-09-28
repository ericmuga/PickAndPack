<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Http\Resources\{OrderResource,AssignmentResource};
use Illuminate\Http\Request;
use App\Models\{Assignment,Line,User};
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        

        $ass= AssignmentResource::collection(Assignment::get());
        $assemblers =User::select('name','id')->get();

        

        $order_parts =DB::table('order_parts')
                        ->select('order_part','description','line_count','order_qty')
                        ->groupBy('order_part','description','line_count','order_qty')


                        ->where('shp_date','>=',Carbon::today()->toDateString())
                        ->get();


      

        return inertia('Assignment/List',compact('ass','assemblers','order_parts'));
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
    public function store(Request $request)
    {
        
       foreach($request->order_parts as $order_part)
       {

         Assignment::create([
                             'assignee_id'=>$request->assignee,
                             'assignor_id'=>$request->user()->id,
                             'part'=>$request->part, 
                             'order_no'=>substr($order_part,0,-2), 
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
