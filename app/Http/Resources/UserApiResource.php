<?php

namespace App\Http\Resources;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\{RoleResource,PermissionResource};
class UserApiResource extends JsonResource
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

        'name'=>$this->name,
        'email'=>$this->email,
        'roles'=>RoleResource::collection($this->whenLoaded('roles')),
        'permissions'=>PermissionResource::collection($this->whenLoaded('permissions'))
      ];
    }
}
