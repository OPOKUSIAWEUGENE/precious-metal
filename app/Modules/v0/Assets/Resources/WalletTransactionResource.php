<?php

namespace App\Modules\v0\Assets\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'group_reference' => $this->group_reference,
            'operation' => $this->operation,
            'direction' => $this->direction,
            'status' => $this->status,
            'wallet_id' => $this->wallet_id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'counterparty_type' => $this->counterparty_type,
            'counterparty_id' => $this->counterparty_id,
            'quote_reference' => $this->quote_reference,
            'asset_amount' => $this->asset_amount,
            'cash_amount' => $this->cash_amount,
            'price_per_gram' => $this->price_per_gram,
            'fee_amount' => $this->fee_amount,
            'total_amount' => $this->total_amount,
            'narration' => $this->narration,
            'metadata' => $this->metadata,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
