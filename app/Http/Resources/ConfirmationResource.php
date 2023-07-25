<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConfirmationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        return ['id'=>$this->id,
                'order_no'=>$this->order_no,
                'part_no'=>$this->part_no,
                'user'=>$this->user->name,
                'created_at'=>$this->created_at,
                'updated_at'=>$this->updated_at,
               ];
    }
}
