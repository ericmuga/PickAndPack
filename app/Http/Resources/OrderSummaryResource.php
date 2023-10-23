<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Order;
use Carbon\Carbon;

class OrderSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       return [
                'order_no'=>$this->order_no,
             'shp_name'=>$this->shp_name,
             'sp_name'=>$this->sp_name,
            'sp_code'=>$this->sp_code,
             'shp_date'=>Carbon::parse($this->shp_date)->toDateString(),
       ];
    }
}
