<?php

namespace App\Http\Resources;

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
      return [
               'order'=>OrderSummaryResource::make($this->whenLoaded('order')),
               'id'=>$this->id,
               'user'=>UserResource::make($this->whenLoaded('user')),
               'part'=>$this->part,
               'packing_time'=>$this->packing_time,
               'packed_at'=>Carbon::parse($this->created_at)->toDateTimeString(),
               'checker'=>UserResource::make($this->whenLoaded('checker'))

      ];

    }
}
