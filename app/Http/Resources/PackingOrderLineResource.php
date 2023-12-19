<?php

namespace App\Http\Resources;
use App\Models\Line;

use Illuminate\Http\Resources\Json\JsonResource;

class PackingOrderLineResource extends JsonResource
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
            'line_no'=>$this->line_no,
            'item_no'=>$this->item_no,
            'item_description'=>$this->item_description,
            'customer_spec'=>$this->customer_spec,
            'part'=>$this->part,
            'barcode'=>$this->barcode,
            'order_qty'=>$this->order_qty,
            'ass_qty'=>$this->ass_qty,
            'exec_qty'=>$this->exec_qty,
           ];
    }
}
