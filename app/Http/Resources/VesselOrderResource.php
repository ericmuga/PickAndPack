<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Order;
class VesselOrderResource extends JsonResource
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
            'shp_name'=>$this->shp_name?:$this->customer_name,
            'lines'=>LineResource::collection($this->whenLoaded('lines')),

        ];
    }
}
