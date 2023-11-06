<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'loader_id'=>$this->loader_id,
            // 'loader'=>$this->loader()->name,
            'driver'=>$this->driver?->name,
            'vehicle'=>$this->vehicle->plate,
            'loader'=>$this->loader?->name,
            'prepacks'=>0,
            'id'=>$this->id,
            'loading_date'=>Carbon::parse($this->created_at)->toDateString(),
        ];

    }
}
