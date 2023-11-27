<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class LoadingSessionResource extends JsonResource
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

            'driver_id'=>$this->driver_id,
            'vehicle_id'=>$this->vehicle_id,
            'loader_id'=>$this->user_id,
            'sp'=>DB::table('sales_people')->select('Name')->where('code',$this->sp_code)->get(),
            'shp_date'=>$this->shp_date,
            'route'=>$this->SalesPerson()->first()->name,
            'driver'=>$this->driver()->first()?->name,
            'vehicle'=>$this->vehicle()->first()->plate,
            'loader'=>\App\Models\User::find($this->user_id)->name,
            'prepacks'=>0,
            'id'=>$this->id,
            'status'=>$this->status,
            'loading_date'=>Carbon::parse($this->created_at)->toDateString(),
            'lines'=>$this->whenLoaded('lines')

        ];

    }
}
