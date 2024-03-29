<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [

                // 'order'=>$this->order->order_no.'|'.$this->order->shp_name,
                // 'part'=>$this->part,
                'assignee'=>$this->assignee->name,
                'assignor'=>$this->assignor->name,
                'time'=>$this->created_at->diffForHumans(),
                'id'=>$this->id,
                'lines_count'=>$this->productLineCount(),
                'lines_weight'=>round($this->productLineWeight(),2),
                'orders_count'=>$this->orderCount(),
                'percentage'=>$this->percentage(),
                'total_time'=>$this->totalTime(),
                'lines'=>$this->whenLoaded('lines')

               ];
    }
}
