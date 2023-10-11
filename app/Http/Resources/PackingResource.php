<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackingResource extends JsonResource
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
                    'item_no'=>$this->line->item_no,
                     'barcode'=>$this->line->barcode,
                     'order_no'=>$this->order_no,
                     'line_no'=>$this->line_no,
                     'packed_qty'=>$this->packed_qty,
                     'packed_pcs'=>$this->packed_pcs,
                     'vessel'=>$this->vessel,
                     'from_vessel'=>$this->from_vessel,
                     'to_vessel'=>$this->to_vessel
              ];
    }
}
