<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{User,Line,Order};

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
                'prepared_by'=>User::select('name')->where('id',$this->user_id)->get(),
                'customer_no'=>Order::select('customer_no')->where('order_no',$this->order_no)->get(),
                'shp_name'=>Order::select('shp_name')->where('order_no',$this->order_no)->get(),
                'sales_person'=>Order::select('sp_code')->where('order_no',$this->order_no)->get().'|'.Order::select('sp_code')->where('order_no',$this->order_no)->get(),
                'line'=>LineResource::make($this->whenLoaded('line')),
                'order'=>OrderResource::make($this->whenLoaded('order')),
                'user'=>UserResource::make($this->whenLoaded('user')),
              ];

    }


}
