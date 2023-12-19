<?php

namespace App\Http\Resources;

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
               'end_time'=>$originalTime->addHours($carbonTime->hour)->addMinutes($carbonTime->minute)->addSeconds($carbonTime->second)->toDateTimeString(),
               'checker'=>UserResource::make($this->whenLoaded('checker')),
               'lines'=>PackingSessionLineResource::collection($this->whenLoaded('lines')),



      ];

    }
}
