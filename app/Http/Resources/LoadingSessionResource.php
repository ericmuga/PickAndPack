<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoadingSessionResource extends JsonResource
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

            'driver_id'=>$this->driver_id,
            'vehicle_id'=>$this->vehicle_id,
            'assistant_loader_id'=>$this->assistant_loader_id,
            'loader_id'=>$this->loader_id,
            'driver'=>$this->driver->name,
            'vehicle'=>$this->vehicle->plate,
            'loader'=>$this->loader->name,
        ];

    }
}
