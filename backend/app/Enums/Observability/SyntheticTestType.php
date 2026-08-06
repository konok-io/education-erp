<?php

declare(strict_types=1);

namespace App\Enums\Observability;

enum SyntheticTestType: string
{
    case LOGIN = 'login';
    case ADMISSION = 'admission';
    case FEE_PAYMENT = 'fee_payment';
    case ATTENDANCE = 'attendance';
    case API = 'api';
    case SEARCH = 'search';
    case CUSTOM = 'custom';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
