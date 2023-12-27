<?php

namespace App\Http\Resources;

// use App\Models\PackingVessel;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingSessionLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $vessel=$this->packing_vessel->code);
        return [
            'id'=>$this->id,
            'item_no'=>$this->item_no,
            'item'=>ItemResource::make($this->item),
            'qty'=>$this->qty,
            'weight'=>$this->weight,
            'packing_vessel'=>$this->whenLoaded('packing_vessel'),
            'from_vessel'=>$this->from_vessel,
            'to_vessel'=>$this->to_vessel,
        ];
    }
}
