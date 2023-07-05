<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{User,Line};

class LinePrepackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this);
        return [
                'id'=>$this->id,
                'prepack_name'=>$this->prepack_name,
                'total_quantity'=>$this->total_quantity,
                'prepack_count'=>$this->prepack_count,
                'batch_no'=>$this->batch_no,
                'order_no'=>$this->order_no,
                'carton_no'=>$this->carton_no,
                'prepack_time'=>Carbon::parse($this->created_at)->toDateTimeString(),
                'created_at'=>$this->created_at,
                'prepared_by'=>$this->user->name,
                'customer_no'=>$this->order->customer_name,
                'shp_name'=>$this->order->shp_name,
                'sales_person'=>$this->order->sp_code.'|'.$this->order->sp_name,
                'line'=>LineResource::make($this->whenLoaded('line')),
                'order'=>OrderResource::make($this->whenLoaded('order')),
                'user'=>UserResource::make($this->whenLoaded('user')),
              ];

    }


}
