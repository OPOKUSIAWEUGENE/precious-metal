<?php

namespace App\Modules\v0\Fleet\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\v0\User\Resources\ProfileResource;

class DriverVehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'vehicle_id' => $this->vehicle_id,
            'profile' => $this->whenLoaded('profile', function() {
                return new ProfileResource($this->profile);
            }),
            'vehicle' => $this->whenLoaded('vehicle', function() {
                return new VehicleResource($this->vehicle);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}