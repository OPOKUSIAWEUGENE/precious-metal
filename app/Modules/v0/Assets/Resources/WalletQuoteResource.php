<?php

namespace App\Modules\v0\Assets\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletQuoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'operation' => $this->operation,
            'status' => $this->status,
            'currency' => $this->currency,
            'cash_amount' => $this->cash_amount,
            'gold_weight' => $this->gold_weight,
            'price_per_gram' => $this->price_per_gram,
            'fee_rate' => $this->fee_rate,
            'fee_amount' => $this->fee_amount,
            'total_payable' => $this->total_payable,
            'total_receivable' => $this->total_receivable,
            'recipient_type' => $this->recipient_type,
            'recipient_id' => $this->recipient_id,
            'expires_at' => $this->expires_at,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
