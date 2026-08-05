<?php

declare(strict_types=1);

namespace App\Http\Resources\Transport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteStopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'stop_name' => $this->stop_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'arrival_time' => $this->arrival_time,
            'departure_time' => $this->departure_time,
            'sequence' => $this->sequence,
            'distance_from_school' => $this->distance_from_school,
            'monthly_fee' => $this->monthly_fee,
            'is_active' => $this->is_active,
        ];
    }
}
