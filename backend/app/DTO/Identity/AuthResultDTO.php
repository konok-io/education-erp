<?php

declare(strict_types=1);

namespace App\DTO\Identity;

use Spatie\DataTransferObject\DataTransferObject;

class AuthResultDTO extends DataTransferObject
{
    public bool $success;
    public string $message;
    public ?string $access_token;
    public ?string $refresh_token;
    public ?string $token_type;
    public ?int $expires_in;
    public ?string $user_id;
    public ?string $session_id;
    public ?array $user;
    public ?array $mfa_required;
    public ?array $metadata;

    public static function success(
        string $accessToken,
        string $refreshToken,
        int $expiresIn,
        string $userId,
        string $sessionId,
        array $user,
        ?array $metadata = null
    ): self {
        return new self(
            success: true,
            message: 'Authentication successful',
            access_token: $accessToken,
            refresh_token: $refreshToken,
            token_type: 'Bearer',
            expires_in: $expiresIn,
            user_id: $userId,
            session_id: $sessionId,
            user: $user,
            mfa_required: null,
            metadata: $metadata,
        );
    }

    public static function mfaRequired(array $mfaData, string $message = 'MFA verification required'): self
    {
        return new self(
            success: false,
            message: $message,
            access_token: null,
            refresh_token: null,
            token_type: null,
            expires_in: null,
            user_id: null,
            session_id: null,
            user: null,
            mfa_required: $mfaData,
            metadata: null,
        );
    }

    public static function failure(string $message, ?array $metadata = null): self
    {
        return new self(
            success: false,
            message: $message,
            access_token: null,
            refresh_token: null,
            token_type: null,
            expires_in: null,
            user_id: null,
            session_id: null,
            user: null,
            mfa_required: null,
            metadata: $metadata,
        );
    }
}
