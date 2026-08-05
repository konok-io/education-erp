<?php

declare(strict_types=1);

namespace App\Http\Resources\Transport;

use App\Models\Transport\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'vehicle_number' => $this->vehicle_number,
            'registration_number' => $this->registration_number,
            'vehicle_type' => $this->vehicle_type,
            'vehicle_type_label' => Vehicle::vehicleTypes()[$this->vehicle_type] ?? $this->vehicle_type,
            'brand' => $this->brand,
            'model' => $this->model,
            'manufacture_year' => $this->manufacture_year,
            'color' => $this->color,
            'engine_number' => $this->engine_number,
            'chassis_number' => $this->chassis_number,
            'seat_capacity' => $this->seat_capacity,
            'purchase_date' => $this->purchase_date,
            'purchase_cost' => $this->purchase_cost,
            'fuel_type' => $this->fuel_type,
            'fuel_type_label' => Vehicle::fuelTypes()[$this->fuel_type] ?? $this->fuel_type,
            'tank_capacity' => $this->tank_capacity,
            'current_odometer' => $this->current_odometer,
            'status' => $this->status,
            'status_label' => Vehicle::statuses()[$this->status] ?? $this->status,
            'image' => $this->image,
            'description' => $this->description,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'is_available' => $this->isAvailable(),
            'current_insurance' => $this->getCurrentInsurance() ? [
                'policy_number' => $this->getCurrentInsurance()->policy_number,
                'expiry_date' => $this->getCurrentInsurance()->expiry_date,
            ] : null,
            'maintenance_due' => $this->getMaintenanceDue(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
