<?php

namespace App\Modules\v0\Trips\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\v0\Trips\Resources\TripResource;
use App\Modules\v0\User\Resources\ProfileResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trip' => $this->whenLoaded('trip', function() {
                return new TripResource($this->trip);
            }),
            'passenger' => $this->whenLoaded('passenger', function() {
                return new ProfileResource($this->passenger);
            }),
            'seat_count' => $this->seat_count,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}