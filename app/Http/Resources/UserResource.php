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
            'user_id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'toekn' => $this->createToken('Token')->accessToken,
            'roles' => $this->roles,
            // 'roles.permissions' => $this->getPermissionsViaRoles()->pluck('name') ?? [],
            // 'permissions' => $this->permissions ?? [],
        ];
    }
}
