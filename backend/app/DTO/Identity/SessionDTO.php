<?php

declare(strict_types=1);

namespace App\DTO\Identity;

use App\Models\Identity\UserSession;
use Spatie\DataTransferObject\DataTransferObject;

class SessionDTO extends DataTransferObject
{
    public string $id;
    public string $user_id;
    public ?string $name;
    public string $device_type;
    public ?string $device_name;
    public ?string $device_os;
    public ?string $device_browser;
    public ?string $ip_address;
    public ?string $location;
    public string $status;
    public bool $is_current;
    public ?string $login_at;
    public ?string $last_activity_at;
    public ?string $token_expires_at;

    public static function fromModel(UserSession $session): self
    {
        return new self(
            id: $session->id,
            user_id: $session->user_id,
            name: $session->name,
            device_type: $session->device_type,
            device_name: $session->device_name,
            device_os: $session->device_os,
            device_browser: $session->device_browser,
            ip_address: $session->ip_address,
            location: $session->location,
            status: $session->status,
            is_current: $session->is_current,
            login_at: $session->login_at?->toIso8601String(),
            last_activity_at: $session->last_activity_at?->toIso8601String(),
            token_expires_at: $session->token_expires_at?->toIso8601String(),
        );
    }

    public static function fromCollection(array $sessions): array
    {
        return array_map(fn($session) => self::fromModel($session), $sessions);
    }
}
