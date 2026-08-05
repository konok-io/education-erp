<?php

declare(strict_types=1);

namespace App\Http\Resources\Transport;

use App\Models\Transport\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'trip_no' => $this->trip_no,
            'vehicle_id' => $this->vehicle?->uuid,
            'vehicle' => $this->vehicle ? [
                'id' => $this->vehicle->uuid,
                'vehicle_number' => $this->vehicle->vehicle_number,
                'vehicle_type' => $this->vehicle->vehicle_type,
            ] : null,
            'driver_id' => $this->driver?->uuid,
            'driver' => $this->driver ? [
                'id' => $this->driver->uuid,
                'full_name' => $this->driver->full_name,
                'phone' => $this->driver->phone,
            ] : null,
            'route_id' => $this->route?->uuid,
            'route' => $this->route ? [
                'id' => $this->route->uuid,
                'route_code' => $this->route->route_code,
                'route_name' => $this->route->route_name,
            ] : null,
            'trip_type' => $this->trip_type,
            'trip_type_label' => Trip::tripTypes()[$this->trip_type] ?? $this->trip_type,
            'trip_date' => $this->trip_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'start_odometer' => $this->start_odometer,
            'end_odometer' => $this->end_odometer,
            'distance' => $this->distance,
            'passenger_count' => $this->passenger_count,
            'status' => $this->status,
            'status_label' => Trip::statuses()[$this->status] ?? $this->status,
            'remarks' => $this->remarks,
            'created_by' => $this->creator ? [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
