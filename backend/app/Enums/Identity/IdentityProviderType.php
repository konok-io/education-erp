<?php

declare(strict_types=1);

namespace App\Enums\Identity;

enum IdentityProviderType: string
{
    case SAML = 'saml';
    case OIDC = 'oidc';
    case OAUTH = 'oauth';
    case LDAP = 'ldap';
    case ACTIVE_DIRECTORY = 'active_directory';
    case AZURE = 'azure';
    case GOOGLE = 'google';
    case GITHUB = 'github';
    case APPLE = 'apple';
    case LINKEDIN = 'linkedin';
    case FACEBOOK = 'facebook';
    case TWITTER = 'twitter';

    public function label(): string
    {
        return match ($this) {
            self::SAML => 'SAML 2.0',
            self::OIDC => 'OpenID Connect',
            self::OAUTH => 'OAuth 2.0',
            self::LDAP => 'LDAP',
            self::ACTIVE_DIRECTORY => 'Active Directory',
            self::AZURE => 'Microsoft Azure AD',
            self::GOOGLE => 'Google Workspace',
            self::GITHUB => 'GitHub',
            self::APPLE => 'Apple ID',
            self::LINKEDIN => 'LinkedIn',
            self::FACEBOOK => 'Facebook',
            self::TWITTER => 'Twitter/X',
        };
    }

    public function getProtocol(): string
    {
        return match ($this) {
            self::SAML => 'saml',
            self::OIDC => 'oidc',
            self::OAUTH, self::AZURE, self::GOOGLE, self::GITHUB, self::APPLE, self::LINKEDIN, self::FACEBOOK, self::TWITTER => 'oauth2',
            self::LDAP, self::ACTIVE_DIRECTORY => 'ldap',
        };
    }
}
