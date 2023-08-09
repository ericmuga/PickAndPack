<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{Pick,Order,Line};

class PickController extends Controller
{

    public function pick(Request $request){


        //this will return a view that allows scanning of a given pick

       //get all orders belonging to a given pick
        //    Pick::when('p$request->search);

        $picks=Pick::select('pick_no')
                   ->when($request->has('search')&&$request->search!='',fn($q)=>$q->where('serial_no',$request->search))
                   ->where('pick_time','>=',Carbon::today()->addDay(-3)->toDateString())
                //    ->orderByDesc('serial_no')
                   ->groupBy('pick_no')
                   ->paginate(15);

        $lines=[];

        if ($picks->count=1)
        {
          //get all line items for that pick
          $orders=Order::query()
                    //    ->confirmed()
                        // ->shipCurrent()
                        ->whereIn('order_no',Pick::select('order_no')->where('pick_no',$picks->first()->pick_no)->get()->pluck('order_no'))
                        ->get()
                        ->pluck('order_no');

        // dd(substr($picks->first()->pick_no,2,1));

          $lines=LineResource::collection(Line::whereIn('order_no',$orders)
                                             ->where('part',substr($picks->first()->pick_no,2,1))
                                             ->orderBy('item_description')
                                             ->get());
        //   dd($lines->first());
        }


        $previous=($request->has('search'))?$request->search:'';

    // dd($picks);

        return inertia('Orders/Pick',compact('picks','previous','lines'));
    }


}
