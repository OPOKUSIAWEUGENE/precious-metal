<?php

namespace App\Modules\v0\Trips\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\v0\Trips\Resources\RouteResource;
use App\Modules\v0\Fleet\Resources\VehicleResource;
use App\Modules\v0\User\Resources\ProfileResource;
use App\Modules\v0\Trips\Resources\RouteStopResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'route' => $this->whenLoaded('route', function() {
                return new RouteResource($this->route);
            }),
            'vehicle' => $this->whenLoaded('vehicle', function() {
                return new VehicleResource($this->vehicle);
            }),
            'driver' => $this->whenLoaded('driver', function() {
                return new ProfileResource($this->driver);
            }),
            'scheduled_at' => $this->scheduled_at,
            'departure_stop' => $this->whenLoaded('departureStop', function() {
                return new RouteStopResource($this->departureStop);
            }),
            'arrival_stop' => $this->whenLoaded('arrivalStop', function() {
                return new RouteStopResource($this->arrivalStop);
            }),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}