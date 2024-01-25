<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use App\Models\Order;

class AssignmentOrderResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
    */
    public function toArray($request)
    {
        $cust='';

        // dd($this->shp_name);
        // if($this->shp_name==''||$this->shp_name==null) $cust=$this->customer_name;
        // else $cust=$this->shp_name;

        return [
            'order_no'=>$this->order_no,
            // 'customer_name'=>$this->shp_name,
            'shp_name'=>$this->shp_name,
            'sp_code'=>$this->sp_code,
            'shp_date'=>Carbon::parse($this->shp_date)->toDateString(),
            'part_a'=>$this->lines()->OfPart('A')->count(),
            'part_b'=>$this->lines()->OfPart('B')->count(),
            'part_c'=>$this->lines()->OfPart('C')->count(),
            'part_d'=>$this->lines()->OfPart('D')->count(),
            'assigned_a'=>$this->assignmentLines()->where('part','=','A')->exists(),
            'assigned_b'=>$this->assignmentLines()->where('part','=','B')->exists(),
            'assigned_c'=>$this->assignmentLines()->where('part','=','C')->exists(),
            'assigned_d'=>$this->assignmentLines()->where('part','=','D')->exists(),
            'confirm_a'=>Order::checkConfirmation($this->order_no,'A'),
            'confirm_b'=>Order::checkConfirmation($this->order_no,'B'),
            'confirm_c'=>Order::checkConfirmation($this->order_no,'C'),
            'confirm_d'=>Order::checkConfirmation($this->order_no,'D'),
            'lines'=>$this->whenLoaded('lines'),
            'confirmations_count'=>$this->whenCounted('confirmations'),
            'assignmentLines_count'=>$this->whenCounted('assignmentLines'),
            'assignmentLines'=>$this->whenLoaded('assignmentLines'),



        ];

    }
}
