<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrepackResource extends JsonResource
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
            'prepack_name'=>$this->item_no.'|'.$this->pack_size,
            'pack_size'=>$this->pack_size,
            'item_no'=>$this->item_no,
            'description'=>$this->name.'|'.$this->item->description,
            'item'=>ItemResource::make($this->whenLoaded('item')),
            'linePrepack_count'=>$this->whenCounted('linePrepacks'),
            'isActive'=>$this->isActive,

        ];
    }
}
