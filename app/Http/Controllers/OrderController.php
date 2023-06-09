<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Http\Resources\{OrderResource,LineResource};
use App\Models\Confirmation;
use App\Models\Line;
use stdClass;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use App\Exports\ConfirmationExport;
use App\Helpers\ColumnListing;
use Maatwebsite\Excel\Facades\Excel;
class OrderController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response


    */


    public function export()
    {
        return Excel::download(new ConfirmationExport, 'confirmations.xlsx');
    }

     public function refresh(Request $request)
     {

        try
         {
            DB::statement('EXEC dbo.refresh');
            return $this->index($request,null);
        }
        catch(QueryException $e){
            $errorMessage = $e->getMessage();

            return back();
        }

     }

      public function dashboard()
      {
        //get stats to show on dashboard


         $data=['todays'=>Order::current()->count(),
                'pending'=>$this->getPending()
      ];
        //return dashboard

        return inertia('Dashboard',$data);

      }

     public function getPending()
     {
        $orders=Order::current()
                     ->execute()
                     ->get();
        $pending=0;
        foreach ($orders as $order)
        {
            if ($order->lines()
                      ->groupBy('order_no')
                      ->groupBy('part')
                      ->count()>$order->confirmations()->count()
                       )

            $pending++;
        }
        return $pending;

    }




    public function index(Request $request,$e=null)
    {

        $orders=OrderResource::collection(Order::query()

                                            //    ->where('shp_date','>=',Carbon::today()->toDateString())
                                               ->when($request->has('search'),fn($q)=>
                                                            $q->where('order_no','like','%'.$request->search)
                                                              ->orWhere('customer_name','like','%'.$request->search.'%')
                                                     )
                                               ->current()
                                               ->orderByDesc('ending_date')
                                               ->orderByDesc('ending_time')
                                               ->with('confirmations')
                                            //    ->withCount()
                                               ->paginate(10)
                                               ->withQuerystring()

                                            );
        $listing=collect((new ColumnListing('orders'))->getColumns())->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');


        return inertia('Orders/List',['orders'=>$orders,'refreshError'=>$e,'columnListing'=>$listing]);
    }

    public function confirmation(Request $request)
    {

        // dd($request->all());
        if ($request->has('order_no','part_no')){

            if (!Order::checkConfirmation($request->order_no,$request->part_no))
               {
                 Confirmation::insert(['order_no'=>$request->order_no,
                                        'part_no'=>$request->part_no,
                                        'user_id'=>$request->user()->name,
                                        'created_at'=>Carbon::now(),
                                        'updated_at'=>Carbon::now()
                                    ]);
                $this->index($request);
               }

        }
    }

    function filter(Request $request)
    {
     //dd($request->all());

         //create a dynamic query

     $searchParams =$request->all();
      $query = Order::query();

      foreach ($searchParams as $key => $value) {
        if ($value !== null) {
            if (is_array($value))
            {

                foreach($value as $k=>$val)
                {
                    if ($k==='from'){
                        $query->where($key,'>=',$val);

                    }
                    else //meaning to
                      $query->where($key,'<=',$val);
                }

            }
            else
            {
               $query->where($key,'like','%'.$value.'%');
            }
          // Add more `when` clauses for other search parameters
        }
      }

     $orders= OrderResource::collection($query->current()
                                               ->orderByDesc('ending_date')
                                               ->orderByDesc('ending_time')
                                               ->with('confirmations')
                                               ->paginate(10)
                                               ->withQuerystring()

                                            );
        $listing=collect((new ColumnListing('orders'))->getColumns())->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');
       return inertia('Orders/List',['orders'=>$orders,'refreshError'=>null,'columnListing'=>$listing]);

     }




      public function assemble(Request $request,$part=null,$sector=null,$spcode=null,$item=null)
      {

          $orderLines=LineResource::collection(Line::query()
                                                ->when($request->has('part')&&$request->part!='All',fn($q)=>$q->OfPart($request->part))
                                                ->when($request->has('item')&&$request->item!='All',fn($q)=>$q->where('item_no',$request->item))
                                                ->whereHas('order',fn($q)=>$q->execute()
                                                                             ->current()
                                                                             ->when($request->has('sector')&&$request->sector!='All',fn($q)=>$q->sector($request->sector))
                                                                             ->when($request->has('spcode')&&$request->spcode!='',fn($q)=>$q->where('sp_code',$request->spcode))
                                                          )
                                                ->orderBy('order_no')
                                                ->paginate(15)
                                                ->appends([$request->all()])
                                                ->withQueryString()
                                            );

    //  $previousInput=$request->all();


    return inertia('Orders/PartLines',
     ['orderLines'=>$orderLines ,
       'previousInput'=>$request->all(),

     ]);
}

/**
* Show the form for creating a new resource.
*
* @return \Illuminate\Http\Response
*/

public function pack(Request $request)
{
    //this will be the view to select the order
    //give a list of all orders ready for packing
    $orders= OrderResource::collection(Order::query()
                                            ->when($request->has('search'),fn($q)=>
                                                        $q->where('order_no','like','%'.$request->search)
                                                        // ->orWhere('customer_name','like','%'.$request->search.'%')
                                                    )
                                            ->current()
                                               ->orderByDesc('ending_date')
                                               ->orderByDesc('ending_time')
                                               ->with('confirmations')
                                               ->paginate(10)
                                               ->withQuerystring()

                                            );
        $listing=collect((new ColumnListing('orders'))->getColumns())->only('customer_name','shp_name','order_no','shp_date','sp_code','ending_date');
       return inertia('Orders/Pack',['orders'=>$orders,'refreshError'=>null,'columnListing'=>$listing]);



}

 public function scanItems(Request $request)
 {
    //get the items that belong to that order and part, send them back to vue
    $orderLines=LineResource::collection(Line::query()
                                            ->where('order_no',$request->order_no)
                                            ->where('part',$request->part_no)
                                            ->orderBy('item_description')
                                            ->paginate(15)
                                            ->appends([$request->all()])
                                            ->withQueryString()
                                        );

                                        //  $previousInput=$request->all();


                                        return inertia('Orders/PartPackLines',
                                        ['orderLines'=>$orderLines ,
                                        'previousInput'=>$request->all(),

                                    ]);
}




public function prepack(Request $request)
{
     dd($request->all());

}


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
    //
}


public function getParts($orderId)
{
    // this function just gets the parts in an order
    if ($orderId=='6161102033915')
    {
        return [
            ['name'=>'B','class'=>'p-button-warning'],
            ['name'=>'C','class'=>'p-button-primary'],

        ];
    }
    else
    return [
        ['name'=>'A','class'=>'p-button-success'],
        ['name'=>'B','class'=>'p-button-warning'],

    ];

}

public function selectOrderPart($id)
{
    //get order id, list the parts of that order
    $order=collect([

        'order'=>[   'No'=>$id,
        'Customer'=>'Majid AlFutaim',
        'SpCode'=>'043',

        'parts'=>$this->getParts($id)]
    ]);

    return inertia('Orders/ShowParts',$order);


}

/**
* Display the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/



public function show($id,$part)
{
    // get the order
    $itemsArray=[
        ['id'=>'J31010101',
        'name'=>'Pork Chipolata 200gms|1210202020',
        'namePlain'=>'Pork Chipolata 200gms',
        'Qty'=>20,
        'Part'=>'B',
        'Comment'=>'Pack In Cartons',
        'Status'=>'Assembled'
    ],


    [
        'id'=>'J31010102',
        'name'=>'Pork Chipolata 1Kg|1210302020',
        'namePlain'=>'Pork Chipolata 1Kg',
        'Qty'=>5,
        'Part'=>'B',
        'Comment'=>'',
        'Status'=>'Assembled'
    ],


    [
        'id'=> 'J31031716',
        'namePlain'=> 'Beef Smokies,1kg-xpt',
        'name'=> 'Beef Smokies,1kg-xpt|6161102035582',
        'Qty'=> '2',
        'Part'=> 'C',
        'Comment'=> '',
        'Status'=> 'Pending Assembly'
    ],
    [
        'id'=> 'J31031712',
        'namePlain'=> 'Jet Sausage, 1 Kg',
        'name'=> 'Jet Sausage, 1 Kg|6161102033656',
        'Qty'=> '6',
        'Part'=> 'C',
        'Comment'=> '',
        'Status'=> 'Pending Assembly'
    ],
    [
        'id'=> 'J31031104',
        'namePlain'=> 'Pork Smokies 900gms',
        'name'=> 'Pork Smokies 900gms|6161102031928',
        'Qty'=> '10',
        'Part'=> 'C',
        'Comment'=> '',
        'Status'=> 'Pending Assembly'
    ],
    [
        'id'=> 'J31031710',
        'namePlain'=> 'Retail Beef Smokies 900gms',
        'name'=> 'Retail Beef Smokies 900gms|6161102031041',
        'Qty'=> '15',
        'Part'=> 'C',
        'Comment'=> '',
        'Status'=> 'Pending Assembly'
    ],
    [
        'id'=> 'J31031101',
        'namePlain'=> 'Pork Smokies, 400gms',
        'name'=> 'Pork Smokies, 400gms|6161102030211',
        'Qty'=> '30',
        'Part'=> 'C',
        'Comment'=> '',
        'Status'=> 'Pending Assembly'
    ],
    [
        'id'=> 'J31031706',
        'namePlain'=> 'Beef Smokies, 400gms Ex long',
        'name'=> 'Beef Smokies, 400gms Ex long|6161102030204',
        'Qty'=> '4',
        'Part'=> 'C',
        'Comment'=> '',
        'Status'=> 'Pending Assembly'
    ],
    [
        'id'=> 'K35016000',
        'namePlain'=> 'Sirimon Cheddar Cheese 2.5kg',
        'name'=> 'Sirimon Cheddar Cheese 2.5kg|6164003476270',
        'Qty'=>'5',
        'Part'=> 'A',
        'Comment'=> '',
        'Status'=> 'Pending Assembly'
        ]


    ];
    $int=0;
    $filteredArray=[];

    foreach($itemsArray as $item)
    {

        if ($item['Part']==$part)
        {
            array_push($filteredArray,$item);
        }

    }




    $order=collect( [ 'order'=> ['No'=>'DSP000005',
    'Customer'=>'Majid AlFutaim',
    'Part'=>$part,
    'SpCode'=>'043',

    'items'=>$filteredArray,
    'batches'=>[
        [

            'code'=>'A303/24'
        ],
        [

            'code'=>'A303/25'
        ],
        [

            'code'=>'A303/26'
        ],
    ],

    'chillers'=>[
        [

            'code'=>'U'
        ],
        [
            'code'=>'V'
        ],

        ]
        ]
    ]);



    return inertia('Orders/Show',$order);
}


public function execute(Request $request)
{
    //get the executed order lines

    //  dd($request->all());
    //save the executed order lines


    //redirect back to scan order
    // get orders pending execution
    $orders=collect(['pendingOrders'=>[

        ['No'=>6161102033915,
        'Customer'=>'Naivas',
        'ShipTo'=>'Westmall',
        'SearchName'=>'6161102033915'.'|'.'Naivas'.'|'.'West Mall',
        'PartAItems'=>0,
        'PartBItems'=>2,
        'PartCItems'=>6,
        'PartDItems'=>0,

    ],
    ['No'=>5038135196799,
    'Customer'=>'Carrefour',
    'ShipTo'=>'Junction',
    'SearchName'=>'5038135196799'.'|'.'Carrefour'.'|'.'Junction',
    'PartAItems'=>0,
    'PartBItems'=>2,
    'PartCItems'=>6,
    'PartDItems'=>0,
],



]]);
return inertia('Scan/ScanOrder',$orders);
}
/**
* Show the form for editing the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/

public function confirm($id)
{
    # code...


    return inertia('Orders/ConfirmAssembly');
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
    //
}
}
