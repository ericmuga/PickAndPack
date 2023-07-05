<?php

namespace App\Http\Resources;

use App\Models\Confirmation;
use Illuminate\Support\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use App\Models\Order;

class OrderResource extends JsonResource
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
            'ended_by'=>Str::replace('FARMERSCHOICE\\','',$this->ended_by),
            'customer_no'=>$this->customer_no,
            'customer_name'=>$this->customer_name,
            'shp_code'=>$this->shp_code,
            'shp_name'=>$this->shp_name,
            'sp_name'=>$this->sp_name,
            'sp_code'=>$this->sp_code,
            'sp_search_name'=>$this->sp_code.'|'.$this->sp_name,
            'shp_date'=>Carbon::parse($this->shp_date)->toDateString(),
            'part_a'=>$this->lines()->OfPart('A')->count(),
            'part_b'=>$this->lines()->OfPart('B')->count(),
            'part_c'=>$this->lines()->OfPart('C')->count(),
            'part_d'=>$this->lines()->OfPart('D')->count(),
            'confirm_a'=>Order::checkConfirmation($this->order_no,'A'),
            'confirm_b'=>Order::checkConfirmation($this->order_no,'B'),
            'confirm_c'=>Order::checkConfirmation($this->order_no,'C'),
            'confirm_d'=>Order::checkConfirmation($this->order_no,'D'),
            'status'=>$this->status,
            'sector'=>$this->sector,
            'ending_time'=>Carbon::parse($this->ending_time)->toTimeString(),
            'ending_date'=>Carbon::parse($this->ending_date)->toDateString(),
            'search_name'=>$this->order_no.'|'.$this->customer_name.'|'.$this->shp_name,
            //    'SearchName'=>'5038135196799'.'|'.'Carrefour'.'|'.'Junction'
            // ],


        ];

    }
}
