<?php

namespace App\Modules\v0\Finances\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->trip_id,
            'operator_id' => $this->operator_id,
            'driver_profile_id' => $this->driver_profile_id,
            'amount' => $this->amount,
            'rate' => $this->rate,
            'gross_amount' => $this->gross_amount,
            'net_amount' => $this->net_amount,
            'status' => $this->status,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'trip' => $this->whenLoaded('trip'),
            'operator' => $this->whenLoaded('operator'),
            'driver_profile' => $this->whenLoaded('driverProfile'),
        ];
    }
}