<?php

namespace App\Modules\v0\User\Resources;

use App\Utils\AppResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'profile_type' => $this->profile_type->value,
            'profile_type_label' => $this->profile_type->label(),
            'role' => $this->when($this->relationLoaded('role'), function () {
                return $this->role ? [
                    'id' => $this->role->id,
                    'name' => $this->role->name,
                    'description' => $this->role->description,
                ] : null;
            }),
            'operator' => $this->when($this->relationLoaded('operator'), function () {
                return $this->operator ? [
                    'id' => $this->operator->id,
                    'name' => $this->operator->name,
                    'type' => $this->operator->type?->value,
                ] : null;
            }),
            'user' => $this->when($this->relationLoaded('user'), function () {
                return $this->user ? [
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                ] : null;
            }),
            'active' => $this->active,
            'current' => $this->current,
            'license' => $this->when($this->profile_type->value === 'DRIVER', $this->license),
            'driver_status' => $this->when($this->profile_type->value === 'DRIVER', fn() => $this->driver_status?->value),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}