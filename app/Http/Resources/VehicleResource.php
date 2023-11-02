<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
                'id'=>$this->id,
                'fleet_no'=>$this->fleet_no,
                'plate'=>$this->plate,
                'tare_weight'=>$this->tare_weight,
                'load_capacity'=>$this->load_capacity,
                'fuel_capacity'=>$this->fuel_capacity,
                'status'=>$this->status,
                'make'=>$this->make,

        ];
    }
}
