<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{Pick, Order, Line, PickOrder};
use App\Http\Resources\{LineResource};
use Carbon\Carbon;


class PickController extends Controller
{

    public function index(Request $request)

    {

        //display all picks for the day

        // $picks=Pick::paginate(15);
        $orders=Order::shipCurrent()->select('order_no')->get();

        $picks = Pick::select('pick_no', 'part')
            //    ->current()
            ->when(
                $request->has('search') && $request->search != '',
                fn ($q) => $q->where('pick_no', 'LIKE', '%' . $request->search)
                    ->orWhereHas('pick_orders', fn ($q) => $q->where('serial_no', 'LIKE', '%' . $request->search)

                    )
            )


            ->paginate(15);


        $previous = ($request->has('search')) ? $request->search : '';

        // dd($picks);

        return inertia('Picks/List', compact('picks', 'previous'));
    }

    public function show(Request $request)
    {
        // this will show a pick with its orders and lines

       $orders=PickOrder::select('order_no')->where('pick_no',$request->pick)->get();



     $orderLines = Line::query()
                        ->whereIn('order_no', $orders)
                        ->where('part', substr($request->pick,2,1))
                        ->withSum('prepacks', 'total_quantity')
                        ->orderBy('item_description')
                        ->paginate(15)
                        ->appends($request->all())
                        ->withQueryString();

        return inertia('Picks/PartPackLines', [
            'orderLines' => LineResource::collection($orderLines),
            'previousInput' => $request->all(),
        ]);
    }

      //return inertia('Picks/Show',compact('orderLines'));



    //}
}
