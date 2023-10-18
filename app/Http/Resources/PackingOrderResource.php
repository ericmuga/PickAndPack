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
            // 'part_a'=>$this->lines()->OfPart('A')->count(),
            // 'part_b'=>$this->lines()->OfPart('B')->count(),
            // 'part_c'=>$this->lines()->OfPart('C')->count(),
            // 'part_d'=>$this->lines()->OfPart('D')->count(),

            //return only manually concluded assemblies
            'assembled_a'=>$this->assembly_sessions()->OfPart('A')->count()==1,
            'assembled_b'=>$this->assembly_sessions()->OfPart('B')->count()==1,
            'assembled_c'=>$this->assembly_sessions()->OfPart('C')->count()==1,
            'assembled_d'=>$this->assembly_sessions()->OfPart('D')->count()==1,



            // 'assigned_a'=>$this->assignmentLines()->where('part','=','A')->exists(),
            // 'assigned_b'=>$this->assignmentLines()->where('part','=','B')->exists(),
            // 'assigned_c'=>$this->assignmentLines()->where('part','=','C')->exists(),
            // 'assigned_d'=>$this->assignmentLines()->where('part','=','D')->exists(),


            // 'confirm_a'=>Order::checkConfirmation($this->order_no,'A'),
            // 'confirm_b'=>Order::checkConfirmation($this->order_no,'B'),
            // 'confirm_c'=>Order::checkConfirmation($this->order_no,'C'),
            // 'confirm_d'=>Order::checkConfirmation($this->order_no,'D'),



        ];
    }

}
