<?php

namespace App\Modules\v0\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\v0\Users\Resources\RoleResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'profile_type' => $this->profile_type,
            'role' => $this->role ? new RoleResource($this->role) : null,
            'operator_id' => $this->operator_id,
            'user_id' => $this->user_id,
            'active' => $this->active,
            'current' => $this->current,
            'license' => $this->license,
            'driver_status' => $this->driver_status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}