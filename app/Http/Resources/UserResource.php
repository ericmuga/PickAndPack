<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
                'user_name'=>$this->name,
                'email'=>$this->email,
                'id'=>$this->id,
                // 'prepacks'=>$this->whenCounted('linePrepacks'),
                'roles'=> RoleResource::collection($this->whenLoaded('roles')),
        ];
    }
}
