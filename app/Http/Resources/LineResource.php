<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LineResource extends JsonResource
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
                //  'id'=>$this->order_no+'|',$this->line_no,
                 'customer_name'=>$this->order->customer_name,
                 'shp_name'=>$this->order->shp_name,
                 'sector'=>$this->order->sector,
                 'sp_code'=>$this->order->sp_code,
                 'line_no'=>$this->line_no,
                 'item_no'=>$this->item_no,
                 'item_description'=>$this->item_description,
                 'customer_spec'=>$this->customer_spec,
                 'part'=>$this->part,
                 'barcode'=>$this->barcode,
                 'order_qty'=>$this->order_qty,
                 'ass_qty'=>$this->ass_qty,
                 'exec_qty'=>$this->exec_qty,
                 'assembler'=>$this->assembler,
                 'checker'=>$this->checker

        ];
    }
}
