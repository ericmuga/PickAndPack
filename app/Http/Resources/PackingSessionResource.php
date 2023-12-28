<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\PackingSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $carbonTime=Carbon::parse($this->packing_time);
        $originalTime=Carbon::parse($this->created_at);
      return [
               'order'=>OrderSummaryResource::make($this->whenLoaded('order')),
               'id'=>$this->id,
               'order_no'=>$this->order_no,
               'packer'=>UserResource::make($this->whenLoaded('user')),
               'part'=>$this->part,
               'packing_time'=>$this->packing_time,
               'start_time'=>Carbon::parse($this->created_at)->toDateTimeString(),
               'end_time'=>Carbon::parse($this->updated_at)->toDateTimeString(),
               'checker'=>UserResource::make($this->whenLoaded('checker')),
               'lines'=>PackingSessionLineResource::collection($this->lines()->with('packing_vessel')->get()),
               'order'=>OrderResource::make($this->whenLoaded('order')),
               'system_entry'=>$this->system_entry



      ];

    }
}
