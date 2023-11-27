<?php

namespace App\Http\Resources;

use App\Models\LoadingLine;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class LoadingSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function orderArrayInSession()
    {
        $array=[];
        foreach( $this->lines as $line)
        {
        //   dd($line);
            $pieces = explode("_",$line->vessel_qr);

               array_push($array,$pieces[0]);
        }

        return $array;
    }

    public function toArray($request)
    {
        return [

            'driver_id'=>$this->driver_id,
            'vehicle_id'=>$this->vehicle_id,
            'loader_id'=>$this->user_id,
            'sp'=>DB::table('sales_people')->select('Name')->where('code',$this->sp_code)->get(),
            'shp_date'=>$this->shp_date,
            'route'=>\App\Models\Order::whereIn('order_no', $this->orderArrayInSession())
                                     ->select('orders.sp_name')
                                      ->first()?->sp_name,
            'driver'=>$this->driver()->first()?->name,
            'vehicle'=>$this->vehicle()->first()->plate,
            'loader'=>\App\Models\User::find($this->user_id)->name,
            'prepacks'=>0,
            'id'=>$this->id,
            'status'=>$this->status,
            'loading_date'=>Carbon::parse($this->created_at)->toDateString(),
            // 'lines'=>$this->whenLoaded('lines'),
            'lines'=>\App\Models\Order::whereIn('order_no', $this->orderArrayInSession())
                                     ->select('orders.order_no','orders.shp_name','loading_lines.vessel_qr','loading_lines.vessel_no','loading_lines.vessel')
                                    ->join('loading_lines', function ($join) {
                                        $join->on(DB::raw('LEFT(loading_lines.vessel_qr, 15)'), '=', 'orders.order_no');
                                    })
                                    ->get(),
            'expected_load'=>\App\Models\Order::where('shp_date',$this->shp_date)
                                             ->where('sp_code',$this->sp_code)
                                             ->select('order_no','shp_name')
                                             ->get()

        ];

    }
}
