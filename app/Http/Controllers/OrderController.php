<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;

class OrderController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        //
    }

    public function scan()
    {

        // get orders pending execution
         $orders=collect(['pendingOrders'=>[

                                                ['No'=>6161102033915,
                                                'Customer'=>'Naivas',
                                                'ShipTo'=>'Westmall',
                                                'SearchName'=>'6161102033915'.'|'.'Naivas'.'|'.'West Mall'

                                                ],
                                                ['No'=>5038135196799,
                                                'Customer'=>'Carrefour',
                                                'ShipTo'=>'Junction',
                                                'SearchName'=>'5038135196799'.'|'.'Carrefour'.'|'.'Junction'
                                                ],



                 ]]);
        return inertia('Scan/ScanOrder',$orders);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
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

 public function confirm()
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
