<?php

declare(strict_types=1);

namespace App\DTO\Transport;

use App\Enums\Transport\VehicleType;
use Illuminate\Http\Request;

final class VehicleDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $vehicle_number,
        public readonly string $registration_no,
        public readonly VehicleType $type = VehicleType::BUS,
        public readonly ?string $brand,
        public readonly ?string $model,
        public readonly int $capacity,
        public readonly ?string $color,
        public readonly ?string $chassis_no,
        public readonly ?string $engine_no,
        public readonly ?string $purchase_date,
        public readonly ?float $purchase_price,
        public readonly ?string $insurance_expiry,
        public readonly ?string $tax_token,
        public readonly ?string $fitness_expiry,
        public readonly ?string $fuel_type,
        public readonly ?float $avg_mileage,
        public readonly string $status = 'active',
        public readonly ?string $photo,
        public readonly ?string $description,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            vehicle_number: $request->input('vehicle_number'),
            registration_no: $request->input('registration_no'),
            type: VehicleType::tryFrom($request->input('type', 'bus')) ?? VehicleType::BUS,
            brand: $request->input('brand'),
            model: $request->input('model'),
            capacity: (int) $request->input('capacity', 50),
            color: $request->input('color'),
            chassis_no: $request->input('chassis_no'),
            engine_no: $request->input('engine_no'),
            purchase_date: $request->input('purchase_date'),
            purchase_price: $request->input('purchase_price') ? (float) $request->input('purchase_price') : null,
            insurance_expiry: $request->input('insurance_expiry'),
            tax_token: $request->input('tax_token'),
            fitness_expiry: $request->input('fitness_expiry'),
            fuel_type: $request->input('fuel_type'),
            avg_mileage: $request->input('avg_mileage') ? (float) $request->input('avg_mileage') : null,
            status: $request->input('status', 'active'),
            photo: $request->input('photo'),
            description: $request->input('description'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'vehicle_number' => $this->vehicle_number,
            'registration_no' => $this->registration_no,
            'type' => $this->type->value,
            'brand' => $this->brand,
            'model' => $this->model,
            'capacity' => $this->capacity,
            'color' => $this->color,
            'chassis_no' => $this->chassis_no,
            'engine_no' => $this->engine_no,
            'purchase_date' => $this->purchase_date,
            'purchase_price' => $this->purchase_price,
            'insurance_expiry' => $this->insurance_expiry,
            'tax_token' => $this->tax_token,
            'fitness_expiry' => $this->fitness_expiry,
            'fuel_type' => $this->fuel_type,
            'avg_mileage' => $this->avg_mileage,
            'status' => $this->status,
            'photo' => $this->photo,
            'description' => $this->description,
        ];
    }
}
