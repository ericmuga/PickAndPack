<?php

namespace App\Exports;

use App\Models\{Confirmation, Order, Orders};
use illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use Maatwebsite\Excel\Concerns\FromCollection;
// use Illuminate\Support\Arr;

class ConfirmationExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

     public $dates=[];
      public function __construct($dates)
      {
        $this->dates=$dates;

      }

      public function collection()
       {
             return  OrderResource::collection(Order::query()
                                            ->whereBetween('ending_date',[$this->dates[0],$this->dates[1]])
                                            ->get()
                                       );

      }

    private function check($part, $confirm)
    {
       if ($part>0)
       {
        if($confirm) return 'Confirmed';
        else return 'Pending';
       }
       else return 'N/A';
    }
}
