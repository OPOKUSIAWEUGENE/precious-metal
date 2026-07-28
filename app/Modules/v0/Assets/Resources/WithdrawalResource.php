<?php

namespace App\Modules\v0\Assets\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_wallet_id' => $this->asset_wallet_id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'rate_id' => $this->rate_id,
            'requested_amount' => $this->requested_amount,
            'asset_amount' => $this->asset_amount,
            'cash_amount' => $this->cash_amount,
            'conversion_rate' => $this->conversion_rate,
            'conversion_type' => $this->conversion_type,
            'status' => $this->status,
            'notes' => $this->notes,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'rejected_by' => $this->rejected_by,
            'rejected_at' => $this->rejected_at,
            'rejection_reason' => $this->rejection_reason,
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner->getAuthIdentifier(),
                    'email' => $this->owner->email ?? null,
                    'first_name' => $this->owner->first_name ?? null,
                    'last_name' => $this->owner->last_name ?? null,
                ];
            }),
            'wallet' => $this->whenLoaded('wallet', function () {
                return [
                    'id' => $this->wallet->id,
                    'wallet_reference' => $this->wallet->wallet_reference,
                    'available_weight' => $this->wallet->available_weight,
                    'reserved_weight' => $this->wallet->reserved_weight,
                    'weight_unit' => $this->wallet->weight_unit,
                ];
            }),
            'rate' => $this->whenLoaded('rate', function () {
                return [
                    'id' => $this->rate->id,
                    'rate' => $this->rate->rate,
                    'status' => $this->rate->status,
                    'effective_at' => $this->rate->effective_at,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
