<?php

namespace App\Modules\v0\GoldWallets\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoldWalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'wallet_reference' => $this->wallet_reference,
            'available_weight' => $this->available_weight,
            'reserved_weight' => $this->reserved_weight,
            'weight_unit' => $this->weight_unit,
            'purity' => $this->purity,
            'status' => $this->status,
            'last_transaction_at' => $this->last_transaction_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'phone_number' => $this->user->phone_number,
                    'role' => $this->user->role,
                    'active' => $this->user->active,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
