<?php

namespace App\Modules\v0\Fleet\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fleet_identifier' => $this->fleet_identifier,
            'operator_id' => $this->operator_id,
            'type' => $this->type,
            'plate_number' => $this->plate_number,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}