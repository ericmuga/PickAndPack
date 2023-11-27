<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VesselResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    /**
     */


    public function toArray($request)
    {
        return [
             'id'=>$this->id,
             'vessel_type'=>$this->vessel_type,
             'vessel_no'=>$this->vessel_no,
             'order_no'=>$this->order_no,
             'part'=>$this->part,
             'shp_name'=>$this->order()->first()->shp_name,
             'qr_code'=>$this->order_no.'_'.$this->part.'_'.$this->vessel_no

        ];
    }
}
