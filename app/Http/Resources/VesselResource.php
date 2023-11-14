<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VesselResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    /**
     *
     * $table->id();
            $table->string('vessel_type');
            $table->string('vessel_no');
            $table->string('order_no');
            $table->string('part');
            $table->foreignIdFor(User::class);
            $table->unsignedBigInteger('packed_by')->references('id')->on('users')->nullable();
            $table->unsignedBigInteger('loaded_by')->references('id')->on('users')->nullable();
            $table->dateTime('loading_time')->nullable();
            $table->timestamps();
     */


    public function toArray($request)
    {
        return [
             'id'=>$this->id,
             'vessel_type'=>$this->vessel_type,
             'vessel_no'=>$this->vessel_no,
             'order_no'=>$this->order_no,
             'part'=>$this->part,
             'shp_name'=>$this->order()->first()->shp_name,
             'qr_code'=>route('loadVessel').'?order_no='.urlencode($this->order_no).'?part='.$this->part.'?vessel_no='.$this->vessel_no

        ];
    }
}
