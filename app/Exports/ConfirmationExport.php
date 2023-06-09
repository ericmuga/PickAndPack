<?php

namespace App\Exports;

use App\Models\{Confirmation,Orders};
use illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;

class ConfirmationExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table('confirmations')
          ->select('confirmations.order_no',
                   'confirmations.part_no',
                   'user_id',
                   'orders.customer_no',
                   'orders.customer_name',
                   'orders.sp_code',
                   'orders.sp_name',
                   'orders.route_code',
                   'orders.shp_name',
                   'orders.shp_date',
                   'confirmations.created_at')
          ->join('orders','orders.order_no','=','confirmations.order_no')
          ->get();

        // return Confirmation::all();
    }
}
