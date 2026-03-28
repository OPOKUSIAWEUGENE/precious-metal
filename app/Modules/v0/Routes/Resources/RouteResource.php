<?php

namespace App\Modules\v0\Routes\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\v0\Routes\Resources\RouteStopResource;

class RouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_stop_id' => $this->start_stop_id,
            'end_stop_id' => $this->end_stop_id,
            'status' => $this->status,
            'route_type' => $this->route_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'startStop' => new RouteStopResource($this->whenLoaded('startStop')),
            'endStop' => new RouteStopResource($this->whenLoaded('endStop')),
        ];
    }
}