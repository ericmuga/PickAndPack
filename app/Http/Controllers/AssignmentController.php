<?php

namespace App\Http\Controllers;

use App\Models\{Order,Assignment};
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Resources\{OrderResource,AssignmentResource}

class AssignmentController extends Controller
{
    
    //list all assignments
    public function index(Request $request)
    {
        
                $orders= OrderResource::collection(Order::query()
                                                        ->when($request->has('search'),fn($q)=>
                                                                $q->where('order_no','like','%'.$request->search)
                                                               
                                                                )
                                                        ->whereHas('confirmations')
                                                        ->where('shp_date','>=',Carbon::now()->toDateString())
                                                        ->orderByDesc('ending_date')
                                                        ->orderByDesc('ending_time')
                                                        ->with('confirmations')
                                                        ->paginate(5)
                                                        ->withQuerystring()

                                                    );


             return inertia('Orders/Assemble',['orders'=>$orders]);

      }

    


}
