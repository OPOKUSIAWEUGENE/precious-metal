<?php

namespace App\Modules\v0\Assets\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetWalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'user_id' => $this->user_id,
            'wallet_reference' => $this->wallet_reference,
            'available_weight' => $this->available_weight,
            'reserved_weight' => $this->reserved_weight,
            'weight_unit' => $this->weight_unit,
            'purity' => $this->purity,
            'status' => $this->status,
            'last_transaction_at' => $this->last_transaction_at,
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner->getAuthIdentifier(),
                    'email' => $this->owner->email ?? null,
                    'first_name' => $this->owner->first_name ?? null,
                    'last_name' => $this->owner->last_name ?? null,
                    'phone_number' => $this->owner->phone_number ?? null,
                    'active' => $this->owner->active ?? null,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
