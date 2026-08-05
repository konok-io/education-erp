<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'role' => $this->whenLoaded('role', fn() => [
                'id' => $this->role->uuid,
                'name' => $this->role->name,
                'display_name' => $this->role->name,
            ]),
            'campus' => $this->whenLoaded('campus', fn() => [
                'id' => $this->campus->uuid,
                'name' => $this->campus->name,
                'code' => $this->campus->code,
            ]),
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'permissions' => $this->whenLoaded('permissions', fn() => 
                $this->getAllPermissions()->pluck('name')
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
