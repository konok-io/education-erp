<?php

declare(strict_types=1);

namespace App\Enums\Identity;

enum AuthMethod: string
{
    case PASSWORD = 'password';
    case EMAIL_LINK = 'email_link';
    case PHONE_OTP = 'phone_otp';
    case MAGIC_LINK = 'magic_link';
    case PASSKEY = 'passkey';
    case BIOMETRIC = 'biometric';
    case OAUTH = 'oauth';
    case SSO = 'sso';
    case API_KEY = 'api_key';
    case TOKEN = 'token';
    case LDAP = 'ldap';
    case SAML = 'saml';

    public function label(): string
    {
        return match ($this) {
            self::PASSWORD => 'Password',
            self::EMAIL_LINK => 'Email Link',
            self::PHONE_OTP => 'Phone OTP',
            self::MAGIC_LINK => 'Magic Link',
            self::PASSKEY => 'Passkey (WebAuthn)',
            self::BIOMETRIC => 'Biometric',
            self::OAUTH => 'OAuth',
            self::SSO => 'Single Sign-On',
            self::API_KEY => 'API Key',
            self::TOKEN => 'Bearer Token',
            self::LDAP => 'LDAP',
            self::SAML => 'SAML',
        };
    }

    public function isMFA(): bool
    {
        return in_array($this, [self::PASSKEY, self::BIOMETRIC]);
    }

    public function isPasswordless(): bool
    {
        return in_array($this, [self::EMAIL_LINK, self::MAGIC_LINK, self::PASSKEY, self::BIOMETRIC]);
    }
}
