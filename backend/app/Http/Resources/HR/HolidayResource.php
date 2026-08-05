<?php

declare(strict_types=1);

namespace App\Http\Resources\HR;

use App\Http\Resources\BaseResource;

class HolidayResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'name_bn' => $this->name_bn,
            'holiday_date' => $this->holiday_date?->toDateString(),
            'holiday_type' => $this->holiday_type,
            'holiday_type_label' => $this->holiday_type ? \App\Models\HR\Holiday::holidayTypes()[$this->holiday_type] ?? $this->holiday_type : null,
            'is_repeating' => $this->is_repeating,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
