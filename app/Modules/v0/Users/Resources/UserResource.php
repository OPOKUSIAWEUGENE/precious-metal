<?php

namespace App\Modules\v0\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\v0\Users\Resources\ProfileResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'social_login_provider' => $this->social_login_provider,
            'social_login_id' => $this->social_login_id,
            'profile_img' => $this->profile_img,
            'active' => $this->active,
            'profiles' => ProfileResource::collection($this->whenLoaded('profiles')),
            'current_profile' => $this->when($this->getCurrentProfile(), function() {
                return new ProfileResource($this->getCurrentProfile());
            }),
            'effective_profile_type' => $this->getEffectiveProfileType(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}