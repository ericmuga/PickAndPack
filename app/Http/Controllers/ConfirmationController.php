<?php

namespace App\Http\Controllers;

use App\Models\{Confirmation};

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\ConfirmationExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;




class ConfirmationController extends Controller
{

 public function unbatched(Request $request)
{
    $shpDate = $request->input('shp_date', now()->toDateString());

    $query = DB::table('orders')
        ->where(function($q) {
            $q->where('is_fully_batched', 0)
              ->orWhereNull('is_fully_batched');
        });

    if (env('APP_ENV') === 'local') {
        $query->whereBetween('shp_date', [now()->subDays(50)->toDateString(), now()->toDateString()])
              ->limit(20);
    } else {
        $query->whereDate('shp_date', $shpDate);
    }

    $orders = $query->orderBy('shp_date')
                    ->orderBy('sp_code')
                    ->get();

    return inertia('Orders/Unbatched', [
        'orders' => $orders,
        'shp_date' => $shpDate,
    ]);
}

public function fetchUnbatched(Request $request)
{
    $shpDate = $request->input('shp_date', now()->toDateString());

    $spCodes = $request->input('sp_codes', []);

    $query = DB::table('orders')
        ->whereNull('batch_number')
        ->whereDate('shp_date', $shpDate);

    if (!empty($spCodes)) {
        $query->whereIn('sp_code', $spCodes);
    }

    $orders = $query->orderBy('shp_date')
                    ->orderBy('sp_code')
                    ->get();

    return response()->json($orders);
}




public function createBatch(Request $request)
{
    info('Creating batch with request data: ' . json_encode($request->all()));
    $spCodes = $request->input('sp_codes');

    if (empty($spCodes)) {
        return back()->withErrors(['No SP Codes selected.']);
    }

    $batchNumber = now()->format('YmdHis'); // unique batch number


    // Get auto-incremented batch_number
    $batchId = DB::table('batches')->insertGetId([
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Mark orders as batched
    // =DB::table('orders')
    //     ->whereIn('sp_code', $spCodes)
    //     ->whereDate('shp_date', '=',$request->input('shp_date'))
    //     ->where('')
    //     ->whereNull('batch_number')
    //     // ->update(['batch_number' => $batchId]);
    //     ->select('order_no')
    //     ->get();

    try {
        // Debug: Log the actual values being used
        $partValue = $request->input('selected_part')[0] ?? $request->input('part');
        $shpDateValue = $request->input('shp_date');
        info('Debug - Part value: ' . json_encode($partValue));
        info('Debug - SP Codes: ' . json_encode($spCodes));
        info('Debug - Ship Date: ' . json_encode($shpDateValue));
        
        // First, let's check what orders match our criteria
        $matchingOrders = DB::table('orders')
            ->select('order_no', 'sp_code', 'shp_date')
            ->whereIn('sp_code', $spCodes)
            ->whereDate('shp_date', '=', $shpDateValue)
            ->get();
        info('Debug - Matching orders: ' . json_encode($matchingOrders->toArray()));
        
        // Then check what lines match
        $matchingLines = DB::table('lines')
            ->where('part', $partValue)
            ->whereIn('order_no', function ($query) use ($spCodes, $shpDateValue) {
                $query->select('order_no')
                    ->from('orders')
                    ->whereIn('sp_code', $spCodes)
                    ->whereDate('shp_date', '=', $shpDateValue);
            })
            ->get();
        info('Debug - Matching lines before update: ' . json_encode($matchingLines->toArray()));
        
        //update lines with batch_number
        $lines = DB::table('lines')
            ->where('part', $partValue)
            ->whereIn('order_no', function ($query) use ($spCodes, $shpDateValue) {
                $query->select('order_no')
                    ->from('orders')
                    ->whereIn('sp_code', $spCodes)
                    ->whereDate('shp_date', '=', $shpDateValue);
            })
            ->update(['batch_number' => $batchId]);
            info('Updated lines' .$lines.' with batch_number: ' . $batchId);      
    } catch (\Exception $e) {
        info('Failed to update lines with batch_number: ' . $e->getMessage());
        throw $e;
    }

    try {
        // Get all affected order_nos from the lines that were just updated
        $affectedOrderNos = DB::table('lines')
            ->where('part', $partValue)
            ->whereIn('order_no', function ($query) use ($spCodes, $shpDateValue) {
                $query->select('order_no')
                    ->from('orders')
                    ->whereIn('sp_code', $spCodes)
                    ->whereDate('shp_date', '=', $shpDateValue);
            })
            ->pluck('order_no')
            ->unique()
            ->toArray();

        info('Affected order_nos: ' . json_encode($affectedOrderNos));
    } catch (\Exception $e) {
        info('Failed to get affected order_nos: ' . $e->getMessage());
        throw $e;
    }

    if (!empty($affectedOrderNos)) {
        try {
            // Find order_nos where all lines have a non-null batch_number
            $fullyBatchedOrderNos = DB::table('lines')
                ->select('order_no')
                ->whereIn('order_no', $affectedOrderNos)
                ->groupBy('order_no')
                ->havingRaw('COUNT(*) = SUM(CASE WHEN batch_number IS NOT NULL THEN 1 ELSE 0 END)')
                ->pluck('order_no')
                ->toArray();
            info('Fully batched order_nos: ' . json_encode($fullyBatchedOrderNos));
        } catch (\Exception $e) {
            info('Failed to find fully batched order_nos: ' . $e->getMessage());
            throw $e;
        }

        if (!empty($fullyBatchedOrderNos)) {
            try {
                // Update orders table to set is_fully_batched = 1 for these order_nos
                DB::table('orders')
                    ->whereIn('order_no', $fullyBatchedOrderNos)
                    ->update(['is_fully_batched' => 1]);
                info('Updated orders as fully batched: ' . json_encode($fullyBatchedOrderNos));
            } catch (\Exception $e) {
                info('Failed to update orders as fully batched: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    try {
        // Aggregate and insert into batched_orders
        $aggregated = DB::table('orders as a')
            ->join('lines as b', 'a.order_no', '=', 'b.order_no')
            ->select(
                'a.sp_code',
                'a.sp_name',
                'a.shp_date',
                'b.item_no',
                'b.item_description',
                'b.part',
                DB::raw('SUM(b.order_qty) as order_qty')
            )
            ->whereIn('a.sp_code', $spCodes)
            ->whereDate('a.shp_date',  $shpDateValue)
            ->groupBy(
                'a.sp_code', 'a.sp_name', 'a.shp_date',
                'b.item_no', 'b.item_description', 'b.part',
                DB::raw('CAST(a.created_at AS TIME)'),
                DB::raw('DATEPART(HOUR, a.updated_at)')
            )
            ->orderBy('a.sp_code')
            ->orderBy('b.part')
            ->orderBy('b.item_no')
            ->get();
        info('Aggregated batched orders: ' . json_encode($aggregated->toArray()));
    } catch (\Exception $e) {
        info('Failed to aggregate batched orders: ' . $e->getMessage());
        throw $e;
    }

    try {
        foreach ($aggregated as $row) {
            DB::table('batched_orders')->insert([
                'batch_number'     => $batchId,
                'sp_code'          => $row->sp_code,
                'sp_name'          => $row->sp_name,
                'shp_date'         => $row->shp_date,
                'item_no'          => $row->item_no,
                'item_description' => $row->item_description,
                'part'             => $row->part,
                'order_qty'        => $row->order_qty,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
        info('Inserted aggregated batched orders into batched_orders table.'. json_encode($aggregated->toArray()));
    } catch (\Exception $e) {
        info('Failed to insert into batched_orders: ' . $e->getMessage());
        throw $e;
    }

    $this->viewBatches();
    //return redirect()->back()->with('success', 'Batch created successfully!');
}


public function viewBatches()
{



    $batches = DB::table('batched_orders')
        ->select(
            'batch_number',
            'sp_code',
            'sp_name',
            'shp_date',
            'item_no',
            'item_description',
            'part',
            DB::raw('SUM(order_qty) as total_qty')
        )
        ->orderByDesc('shp_date')
        ->orderBy('batch_number')
        ->orderBy('item_no')
        ->groupBy(
            'batch_number',
            'sp_code',
            'sp_name',
            'shp_date',
            'item_no',
            'item_description',
            'part'
        )
        ->get();

    return inertia('Orders/Batches', [
        'batches' => $batches,
    ]);
}


    // use ExcelExportTrait;
public function download(Request $request)
    {
        return Excel::download(new ConfirmationExport($request), 'confirmations.xlsx');
    }


    public function refresh(Request $request)
    {

        try
        {
            // dd('here');
            DB::statement('EXEC dbo.refresh');
            $this->index($request);
        }
        catch(QueryException $e){
            $errorMessage = $e->getMessage();

            return back();
        }

    }



    public function index(Request $request)
    {
        $orders = DB::table('pending_confirmation')
                    ->select(
                        'order_no',
                        'shp_date',
                        'sp_code',
                        'sp_name',
                        'shp_name',
                        'A_Count',
                        'B_Count',
                        'C_Count',
                        'D_Count',
                        'A_Confirmation_Count',
                        'B_Confirmation_Count',
                        'C_Confirmation_Count',
                        'D_Confirmation_Count'
                    )
                    ->where('shp_date', '>=', now()->toDateString())
                    ->orderByDesc('ending_date')
                    ->orderByDesc('ending_time')
                    //->limit(20)
                    ->get();

    return inertia('Orders/List', [
            'orders' => $orders,
            'previousInput'=>$request->all(),

        ]);
    }



    public function store(Request $request)
    {
             $confirmation=Confirmation::updateOrCreate(['order_no'=>$request->order_no,
                                                            'part_no'=>$request->part_no,
                                                        ],
                                                        ['order_no'=>$request->order_no,
                                                         'part_no'=>$request->part_no,
                                                         'user_id'=>$request->user()->name,
                                                        ]);

         $confirmed=false;
             $rec=DB::table('pending_confirmation')
                    ->where('order_no',$request->order_no)
                    ->first();

              if ($rec!==null)
              {
                    if(($rec->A_Confirmation_Count+
                        $rec->B_Confirmation_Count+
                        $rec->C_Confirmation_Count+
                        $rec->D_Confirmation_Count
                    )>=
                    (
                        $rec->A_Count+
                        $rec->B_Count+
                        $rec->C_Count+
                        $rec->D_Count
                    ))
                    {
                        DB::table('orders')
                        ->where('order_no',$request->order_no)
                        ->update(['confirmed'=>1]);
                        $confirmed=true;

                    }
                }
                else $confirmed=true;


       return response()->json(compact('confirmed'));

   }
}
