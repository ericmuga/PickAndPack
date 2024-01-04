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
            'shp_name'=>$this->shp_name,
            'orderNo'=>$this->order_no.'|'.$this->shp_name,
            'sp'=>$this->sp_code.'|'.$this->sp_name,
            'lines'=>LineResource::collection($this->whenLoaded('lines')),

        ];
    }
}
