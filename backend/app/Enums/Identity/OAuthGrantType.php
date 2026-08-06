<?php

declare(strict_types=1);

namespace App\Enums\Identity;

enum OAuthGrantType: string
{
    case AUTHORIZATION_CODE = 'authorization_code';
    case CLIENT_CREDENTIALS = 'client_credentials';
    case PASSWORD = 'password';
    case REFRESH_TOKEN = 'refresh_token';
    case IMPLICIT = 'implicit';
    case DEVICE_CODE = 'device_code';

    public function label(): string
    {
        return match ($this) {
            self::AUTHORIZATION_CODE => 'Authorization Code',
            self::CLIENT_CREDENTIALS => 'Client Credentials',
            self::PASSWORD => 'Resource Owner Password',
            self::REFRESH_TOKEN => 'Refresh Token',
            self::IMPLICIT => 'Implicit',
            self::DEVICE_CODE => 'Device Code',
        };
    }

    public function requiresClientSecret(): bool
    {
        return in_array($this, [
            self::AUTHORIZATION_CODE,
            self::CLIENT_CREDENTIALS,
            self::PASSWORD,
        ]);
    }

    public function returnsRefreshToken(): bool
    {
        return in_array($this, [
            self::AUTHORIZATION_CODE,
            self::REFRESH_TOKEN,
        ]);
    }
}
