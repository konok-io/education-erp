<?php

declare(strict_types=1);

namespace App\Enums\Integration;

enum IntegrationStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ERROR = 'error';
    case PENDING = 'pending';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::ERROR => 'Error',
            self::PENDING => 'Pending',
        };
    }
}

enum IntegrationType: string
{
    case PAYMENT = 'payment';
    case SMS = 'sms';
    case EMAIL = 'email';
    case SSO = 'sso';
    case API = 'api';
    case WEBHOOK = 'webhook';
    case FILE_TRANSFER = 'file_transfer';

    public function label(): string
    {
        return match($this) {
            self::PAYMENT => 'Payment Gateway',
            self::SMS => 'SMS Gateway',
            self::EMAIL => 'Email Service',
            self::SSO => 'Single Sign-On',
            self::API => 'API Integration',
            self::WEBHOOK => 'Webhook',
            self::FILE_TRANSFER => 'File Transfer',
        };
    }
}
