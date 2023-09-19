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
        /*

                                            'item_no':form.item_no,
                                           'assembled_qty':form.assembled_qty,
                                           'order_qty':form.order_qty,
                                           'prepacks_total_quantity':form.prepacks_total_quantity,
                                           'item_description':form.item_description,
                                           'barcode':form.barcode,
                                            'item_no':form.item_no,
                                            'order_no':form.order_no,
                                            'line_no':form.line_no,
                                            'packed_qty':form.packed_qty,
                                            'packed_pcs':form.packed_pcs,
                                            'carton_no':form.carton_no,
                                            'vessel':form.vessel,
                                            'from_vessel':form.from_vessel,
                                            'to_vessel':form.to_vessel,
        */

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
