<?php

declare(strict_types=1);

namespace App\Enums\Identity;

enum MFAType: string
{
    case TOTP = 'totp';
    case SMS = 'sms';
    case EMAIL = 'email';
    case PUSH = 'push';
    case SECURITY_KEY = 'security_key';
    case BIOMETRIC = 'biometric';
    case BACKUP_CODE = 'backup_code';

    public function label(): string
    {
        return match ($this) {
            self::TOTP => 'Authenticator App (TOTP)',
            self::SMS => 'SMS OTP',
            self::EMAIL => 'Email OTP',
            self::PUSH => 'Push Notification',
            self::SECURITY_KEY => 'Security Key (FIDO2)',
            self::BIOMETRIC => 'Biometric',
            self::BACKUP_CODE => 'Backup Code',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TOTP => 'safety',
            self::SMS => 'mobile',
            self::EMAIL => 'mail',
            self::PUSH => 'bell',
            self::SECURITY_KEY => 'key',
            self::BIOMETRIC => 'fingerprint',
            self::BACKUP_CODE => 'safety-certificate',
        };
    }

    public function isPrimary(): bool
    {
        return in_array($this, [self::TOTP, self::SMS, self::SECURITY_KEY, self::BIOMETRIC]);
    }
}
