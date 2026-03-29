<?php

namespace App\Modules\v0\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'social_login_provider' => $this->social_login_provider,
            'profile_img' => $this->profile_img,
            'active' => $this->active,
            'effective_profile_type' => $this->getEffectiveProfileType()->value,
            'current_profile' => $this->when($this->relationLoaded('profiles') && $this->currentProfile(), function () {
                $profile = $this->currentProfile();
                return [
                    'id' => $profile->id,
                    'profile_type' => $profile->profile_type->value,
                    'role' => $profile->role ? [
                        'id' => $profile->role->id,
                        'name' => $profile->role->name,
                    ] : null,
                    'operator' => $profile->operator ? [
                        'id' => $profile->operator->id,
                        'name' => $profile->operator->name,
                    ] : null,
                ];
            }),
            'profiles' => $this->when($this->relationLoaded('profiles'), function () {
                return $this->profiles->map(function ($profile) {
                    return [
                        'id' => $profile->id,
                        'profile_type' => $profile->profile_type->value,
                        'active' => $profile->active,
                        'current' => $profile->current,
                        'role' => $profile->role ? [
                            'id' => $profile->role->id,
                            'name' => $profile->role->name,
                        ] : null,
                    ];
                });
            }),
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}