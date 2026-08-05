<?php

declare(strict_types=1);

namespace App\Http\Resources\Transport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'route_code' => $this->route_code,
            'route_name' => $this->route_name,
            'starting_point' => $this->starting_point,
            'ending_point' => $this->ending_point,
            'distance' => $this->distance,
            'estimated_time' => $this->estimated_time,
            'monthly_fee' => $this->monthly_fee,
            'description' => $this->description,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'stops' => RouteStopResource::collection($this->whenLoaded('stops')),
            'total_stops' => $this->when(!$this->relationLoaded('stops'), function () {
                return $this->stops()->count();
            }),
            'active_assignments' => $this->when(!$this->relationLoaded('assignments'), function () {
                return $this->getActiveAssignmentsCount();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
