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

    public function fetchDataAndSave(Request $request)
    {
        // Get query string parameters
        // dd($request->all());

        $company = $request->input('company');
        // $receivedDate = $request->input('received_date');
        $receivedDate = Carbon::today()->toDateString();
        $shpDate = $request->input('shp_date');
        $custNo = $request->input('cust_no');
        $key = '412cce7c-a737-4d01-b929-534fcc80e79d';


       $customers=[404,240,258,913,914,420,823,824];


       for($i=0; $i<count($customers); $i++ )
       {


        // Make GET request to external API
        // dd($customers[$i]);
        $response = Http::get('https://fchoice-endpoint-prod.docwyn.com/?api_key='.$key.'&company='.$company.'&recieved_date='.$receivedDate.'&cust_no='.$customers[$i]);
        //   dd($response->json());

        // Check if the request was successful (status code 200)
        // if ($response->successful()) {
            // Save the response data into your database table
//  dd($response);

            $responseData = $response->json(); // Assuming the response is in JSON format
              ///
            // dd($responseData);

            foreach ($responseData as $data)
            {

                  // $date = Carbon::createFromFormat('d/m/y', $data['shp_date']);
                  // $formattedDate = dd($date->format('Y-m-d'));
                  // dd($formattedDate);

              if (is_array($data))
              if (array_key_exists('ext_doc_no',$data))

                if(!ImportedOrder::where('ext_doc_no',$data['ext_doc_no'])
                                 ->where('item_no')
                                ->exists()

                  )
                if ($data['uom_code']!='nan')
                 ImportedOrder::create([
                    'company' => $data['company'],
                    'cust_no' => $data['cust_no'],
                    'cust_spec' => $data['cust_spec'],
                    'ext_doc_no' => $data['ext_doc_no'],
                    'item_no' => $data['item_no'],
                    'item_spec' => $data['item_spec'],
                    'line_no' => $data['line_no'],
                    'quantity' => floatval($data['quantity']),
                    'shp_code' => $data['shp_code'],
                    // 'shp_date' => $data['shp_date'],
                    'shp_date' => Carbon::tomorrow()->toDateString(),
                    'sp_code' => $data['sp_code'],
                    'uom_code' => $data['uom_code'],
                ]);
            }



           // return response()->json(['message' => 'Data saved successfully']);

      //  } else {
            // Handle the case when the request was not successful
            //return response()->json(['error' => 'Failed to fetch data from the external API'], $response->status());
        }

       return inertia('API/Create');
   }

  }

// }

