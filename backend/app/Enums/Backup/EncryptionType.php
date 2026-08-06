<?php

declare(strict_types=1);

namespace App\Enums\Backup;

enum EncryptionType: string
{
    case NONE = 'none';
    case AES_256 = 'aes-256';
    case AES_128 = 'aes-128';
    case RSA_4096 = 'rsa-4096';
    case RSA_2048 = 'rsa-2048';
    case TLS_1_3 = 'tls-1.3';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'No Encryption',
            self::AES_256 => 'AES-256',
            self::AES_128 => 'AES-128',
            self::RSA_4096 => 'RSA-4096',
            self::RSA_2048 => 'RSA-2048',
            self::TLS_1_3 => 'TLS 1.3',
        };
    }

    public function isEnabled(): bool
    {
        return $this !== self::NONE;
    }
}
