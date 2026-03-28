<?php

namespace App\Modules\v0\Finances\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettlementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'operator_id' => $this->operator_id,
            'driver_profile_id' => $this->driver_profile_id,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'total_amount' => $this->total_amount,
            'commission_amount' => $this->commission_amount,
            'net_amount' => $this->net_amount,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'processed_at' => $this->processed_at,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'operator' => $this->whenLoaded('operator'),
            'driver_profile' => $this->whenLoaded('driverProfile'),
            'commissions' => $this->whenLoaded('commissions'),
        ];
    }
}