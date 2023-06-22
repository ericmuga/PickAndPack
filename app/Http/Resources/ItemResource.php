<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
                   'item_no'=>$this->item_no,
                   'barcode'=>$this->barcode,
                   'description'=>$this->description,
                   'posting_group'=>$this->posting_group,
                   'prepacks'=>$this->whenLoaded('prepacks'),


        ];


    }
}
