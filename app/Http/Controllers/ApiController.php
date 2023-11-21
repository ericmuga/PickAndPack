<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ImportedOrder; // Replace ImportedOrder with the actual model you want to use
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Theme;

class ApiController extends Controller
{
   public function makeCall()
   {
    return inertia('API/Create');


   }

//    public function extractIntegerPart($stringValue)
//     {
//         // Convert the string to a float
//         $floatValue = floatval($stringValue);

//         // Extract the integer part
//         $integerPart = intval($floatValue);

//         return $integerPart;
//     }

    public function fetchDataAndSave(Request $request)
    {
        $company = $request->has('company')?$request->company:'FCL';
        // $receivedDate = $request->input('received_date');
        $receivedDate = Carbon::today()->toDateString();
        $shpDate = $request->input('shp_date');
        $custNo = $request->input('cust_no');
        $key = '412cce7c-a737-4d01-b929-534fcc80e79d';


       $customers=[404,240,258,913,914,420,823,824];

       for($i=0; $i<count($customers); $i++ )
       {


        $response = Http::get('https://fchoice-endpoint-prod.docwyn.com/?api_key='.$key.'&company='.$company.'&recieved_date='.$receivedDate.'&cust_no='.$customers[$i]);
        $responseData = $response->json(); // Assuming the response is in JSON format

        $extdocItem='';
        //make collection
        $collection = collect(json_decode($responseData, true));
        //sort by columns
        $sortedData = $collection->orderBy('ext_doc_no')->orderBy('item_no')->values();
        //get json back
        // $sortedJsonResponse = json_encode($sortedData);
        //fresh array for each customer
        $array_to_insert=[];

        $extdocItem='';

        foreach ($sortedData as $data)
            {

               if($extdocItem!= $data['ext_doc_no'].$data['item_no'])

               $extdocItem!= $data['ext_doc_no'].$data['item_no'];
               else continue;

              if (is_array($data))
              if (array_key_exists('ext_doc_no',$data))

                // if(!ImportedOrder::where('ext_doc_no',$data['ext_doc_no'])
                //                  ->where('item_no',$data['item_no'])
                //                 ->exists()

                //   )

                if ($data['uom_code']!='nan')

                array_push($array_to_insert,
                   [
                    'company' => $data['company'],
                    'cust_no' => $data['cust_no'],
                    'cust_spec' => $data['cust_spec'],
                    'ext_doc_no' => $data['ext_doc_no'],
                    'item_no' => $data['item_no'],
                    'item_spec' => $data['item_spec'],
                    'line_no' => $data['line_no'],
                    // 'quantity' =>in_array($data['uom_code'],['PCS','PC'])?$this->extractIntegerPart(abs(floatval($data['quantity']))):abs(floatval($data['quantity'])),
                    'quantity' =>abs(intval($data['quantity'])),
                    'shp_code' => $data['shp_code'],
                    'shp_date' => Carbon::tomorrow()->toDateString(),
                    'sp_code' => $data['sp_code'],
                    'uom_code' =>$data['uom_code']
                   ]);


                //  ImportedOrder::create([
                //     'company' => $data['company'],
                //     'cust_no' => $data['cust_no'],
                //     'cust_spec' => $data['cust_spec'],
                //     'ext_doc_no' => $data['ext_doc_no'],
                //     'item_no' => $data['item_no'],
                //     'item_spec' => $data['item_spec'],
                //     'line_no' => $data['line_no'],
                //     'quantity' => >in_array($data['uom_code'],['PCS','PC'])?extractIntegerPart(abs(floatval($data['quantity']))):abs(floatval($data['quantity'])),
                //     'shp_code' => $data['shp_code'],
                //     // 'shp_date' => $data['shp_date'],
                //     'shp_date' => Carbon::tomorrow()->toDateString(),
                //     'sp_code' => $data['sp_code'],
                //     'uom_code' =>$data['uom_code'],['PCS','PC'],
                // ]);
            }

            //bulk insert
            ImportedOrder::upsert($array_to_insert,['item_no','ext_doc_no']);





      //  } else {
            // Handle the case when the request was not successful
            //return response()->json(['error' => 'Failed to fetch data from the external API'], $response->status());
        }

     //  return inertia('API/Create');

       return response()->json(['message' => 'Data saved successfully']);
   }

  }

// }

