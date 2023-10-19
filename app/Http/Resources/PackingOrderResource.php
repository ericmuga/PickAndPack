<?php

namespace App\Http\Resources;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingOrderResource extends JsonResource
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
             'shp_name'=>$this->shp_name,
             'sp_name'=>$this->sp_name,
            'sp_code'=>$this->sp_code,
             'shp_date'=>Carbon::parse($this->shp_date)->toDateString(),

            'assembled_a'=>$this->assembly_sessions()->OfPart('A')->count()==1,
            'assembled_b'=>$this->assembly_sessions()->OfPart('B')->count()==1,
            'assembled_c'=>$this->assembly_sessions()->OfPart('C')->count()==1,
            'assembled_d'=>$this->assembly_sessions()->OfPart('D')->count()==1,

            'packed_a'=>$this->packing_sessions()->OfPart('A')->count()==1,
            'packed_b'=>$this->packing_sessions()->OfPart('B')->count()==1,
            'packed_c'=>$this->packing_sessions()->OfPart('C')->count()==1,
            'packed_d'=>$this->packing_sessions()->OfPart('D')->count()==1,





        ];
    }

}
