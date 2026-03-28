<?php

namespace App\Modules\v0\Trips\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\v0\Trips\Resources\RouteStopResource;

class RouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_stop' => $this->whenLoaded('startStop', function() {
                return new RouteStopResource($this->startStop);
            }),
            'end_stop' => $this->whenLoaded('endStop', function() {
                return new RouteStopResource($this->endStop);
            }),
            'status' => $this->status,
            'route_type' => $this->route_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}